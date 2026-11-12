<?php
// controllers/user/PublicProjectController.php — Projects page na nakikita ng bisita
//
// Route: 'projects' → GET = index()
// Kinukuha ang mga project na in-add sa admin (portproject + portproject_image).

if (!defined('ROOT_PATH')) {
    exit('No direct access');
}

class PublicProjectController extends Controller
{
    /** GET — lahat ng projects, kasama ang lahat ng larawan ng bawat isa */
    public function index(): array
    {
        $projects = db_all(
            'SELECT id, name, description, image, created_at
               FROM portproject
              ORDER BY id DESC'
        );

        // Kunin ang lahat ng larawan sa ISANG query (hindi isa-isang query bawat project)
        $imagesByProject = [];
        if ($projects) {
            $ids  = array_map('intval', array_column($projects, 'id'));
            $rows = db_all(
                'SELECT project_id, image
                   FROM portproject_image
                  WHERE project_id IN (' . db_marks($ids) . ')
                  ORDER BY project_id, sort_order, id',
                $ids
            );
            foreach ($rows as $row) {
                $imagesByProject[(int) $row['project_id']][] = img($row['image']);
            }
        }

        // Idikit ang mga larawan sa bawat project (cover lang kung walang nasa portproject_image)
        foreach ($projects as &$project) {
            $project['images'] = $imagesByProject[(int) $project['id']] ?? [img($project['image'])];
        }
        unset($project);

        return $this->view('user/projects', ['projects' => $projects]);
    }
}