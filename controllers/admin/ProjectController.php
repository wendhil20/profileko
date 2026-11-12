<?php
// controllers/admin/ProjectController.php — projects (tables: portproject + portproject_image)
//
// Routes:
//   'adminprojects'      → GET  = index()   (form + listahan)
//                          POST = store()   (bagong project + maraming larawan)
//   'adminprojects/edit' → GET  = edit()    (?id=5 — form na may laman)
//                          POST = update()  (palitan ang name/description, magdagdag/magtanggal ng larawan)

if (!defined('ROOT_PATH')) {
    exit('No direct access');
}

class ProjectController extends Controller
{
    private const MAX_MB     = 10;   // laki ng bawat larawan
    private const MAX_IMAGES = 10;   // ilang larawan bawat project

    /* =========================================================
     |  LISTAHAN + ADD
     * ========================================================= */

    /** GET — ipakita ang form at lahat ng projects */
    public function index(array $errors = []): array
    {
        $projects = db_all(
            'SELECT p.id, p.name, p.description, p.image, p.created_at, COUNT(pi.id) AS image_count
               FROM portproject p
               LEFT JOIN portproject_image pi ON pi.project_id = p.id
              GROUP BY p.id
              ORDER BY p.id DESC'
        );

        return $this->view('admin/page-2/projects', [
            'projects'  => $projects,
            'errors'    => $errors,
            'maxImages' => self::MAX_IMAGES,
            'maxMb'     => self::MAX_MB,
        ]);
    }

    /** POST — i-validate, i-upload ang mga larawan, tapos i-insert */
    public function store(): array
    {
        if ($error = $this->formError()) {
            return $this->index(['form' => $error]);
        }

        $name        = input('name');
        $description = input('description');
        $files       = files('images');

        $errors = $this->validate($name, $description);
        if (!$files) {
            $errors['images'] = 'Please choose at least one image.';
        } elseif (count($files) > self::MAX_IMAGES) {
            $errors['images'] = 'You can upload up to ' . self::MAX_IMAGES . ' images.';
        }
        if ($errors) {
            return $this->index($errors);
        }

        try {
            $images = upload_images($files, 'projects', self::MAX_MB);
        } catch (RuntimeException $ex) {
            return $this->index(['images' => $ex->getMessage()]);
        }

        try {
            db_transaction(function () use ($name, $description, $images) {
                $projectId = db_insert('portproject', [
                    'name'        => $name,
                    'description' => $description,
                    'image'       => $images[0],          // cover = unang larawan
                ]);
                $this->insertImages($projectId, $images, 0);
            });
        } catch (Throwable $ex) {
            delete_images($images);                     // wag iwanan ang mga larawan kung pumalya ang save
            error_log('Insert project failed: ' . $ex->getMessage());
            return $this->index(['form' => 'Could not save the project. Please try again.']);
        }

        flash('success', 'Project "' . $name . '" was added with ' . count($images) . ' image(s).');
        redirect('adminprojects');                      // para hindi ma-submit ulit kapag ni-refresh
    }

    /* =========================================================
     |  EDIT + UPDATE
     * ========================================================= */

    /** GET — form na may laman ng project */
    public function edit(array $errors = []): array
    {
        $project = $this->findOrFail();

        return $this->view('admin/page-2/project-edit', [
            'project'   => $project,
            'images'    => $this->imagesOf($project['id']),
            'errors'    => $errors,
            'maxImages' => self::MAX_IMAGES,
            'maxMb'     => self::MAX_MB,
        ]);
    }

    /** POST — i-save ang mga pagbabago */
    public function update(): array
    {
        $project = $this->findOrFail();
        $id      = (int) $project['id'];

        if ($error = $this->formError()) {
            return $this->edit(['form' => $error]);
        }

        $name        = input('name');
        $description = input('description');
        $files       = files('images');

        // Mga larawang tatanggalin — dapat pag-aari ng project na ito
        $current   = $this->imagesOf($id);
        $currentId = array_map('intval', array_column($current, 'id'));
        $removeIds = array_values(array_intersect(input_ids('remove'), $currentId));
        $remaining = count($current) - count($removeIds) + count($files);

        $errors = $this->validate($name, $description);
        if ($remaining < 1) {
            $errors['images'] = 'A project needs at least one image.';
        } elseif ($remaining > self::MAX_IMAGES) {
            $errors['images'] = 'A project can have up to ' . self::MAX_IMAGES . ' images.';
        }
        if ($errors) {
            return $this->edit($errors);
        }

        try {
            $newImages = upload_images($files, 'projects', self::MAX_MB);
        } catch (RuntimeException $ex) {
            return $this->edit(['images' => $ex->getMessage()]);
        }

        $removedPaths = array_column(
            array_filter($current, fn($img) => in_array((int) $img['id'], $removeIds, true)),
            'image'
        );

        try {
            db_transaction(function () use ($id, $name, $description, $removeIds, $newImages, $current) {
                if ($removeIds) {
                    db_delete('portproject_image', 'project_id = ? AND id IN (' . db_marks($removeIds) . ')', [$id, ...$removeIds]);
                }

                $nextOrder = $current ? max(array_column($current, 'sort_order')) + 1 : 0;
                $this->insertImages($id, $newImages, $nextOrder);

                // Cover = unang natitirang larawan
                $cover = db_one('SELECT image FROM portproject_image WHERE project_id = ? ORDER BY sort_order, id LIMIT 1', [$id]);

                db_update('portproject', [
                    'name'        => $name,
                    'description' => $description,
                    'image'       => $cover['image'],
                ], 'id = ?', [$id]);
            });
        } catch (Throwable $ex) {
            delete_images($newImages);
            error_log('Update project failed: ' . $ex->getMessage());
            return $this->edit(['form' => 'Could not save the changes. Please try again.']);
        }

        delete_images($removedPaths);                   // saka lang buburahin ang files kapag na-save na

        flash('success', 'Project "' . $name . '" was updated.');
        redirect('adminprojects/edit?id=' . $id);
    }

    /* =========================================================
     |  PRIVATE
     * ========================================================= */

    /** Hanapin ang project galing sa ?id= — kapag wala, balik sa listahan */
    private function findOrFail(): array
    {
        $id      = (int) ($_GET['id'] ?? 0);
        $project = $id > 0 ? db_one('SELECT * FROM portproject WHERE id = ?', [$id]) : null;

        if (!$project) {
            flash('error', 'Project not found.');
            redirect('adminprojects');
        }
        return $project;
    }

    /** Lahat ng larawan ng isang project */
    private function imagesOf(int $projectId): array
    {
        return db_all(
            'SELECT id, image, sort_order FROM portproject_image WHERE project_id = ? ORDER BY sort_order, id',
            [$projectId]
        );
    }

    /** I-save ang listahan ng larawan sa portproject_image */
    private function insertImages(int $projectId, array $images, int $startOrder): void
    {
        foreach ($images as $i => $path) {
            db_insert('portproject_image', [
                'project_id' => $projectId,
                'image'      => $path,
                'sort_order' => $startOrder + $i,
            ]);
        }
    }

    /** Problema sa mismong form (sobrang laki o expired) — null kung ok */
    private function formError(): ?string
    {
        if (post_too_large()) {
            return 'The images are too large in total. Upload fewer images at a time.';
        }
        if (!csrf_check()) {
            return 'This form has expired. Please refresh the page and try again.';
        }
        return null;
    }

    /** Validation ng name at description */
    private function validate(string $name, string $description): array
    {
        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Please enter the project name.';
        } elseif (mb_strlen($name) > 150) {
            $errors['name'] = 'Name is too long (max 150 characters).';
        }

        if (mb_strlen($description) > 2000) {
            $errors['description'] = 'Description is too long (max 2000 characters).';
        }

        return $errors;
    }
}