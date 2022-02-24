<?php
namespace App\Controller\Admin;

use App\Controller\Controller;
use App\Model\CommentsModel;
use App\Model\PostsModel;
use App\Model\UserModel;

class CommentsController extends Controller
{

    /**
     * @var string
     */
    protected $title = 'Gestion des commentaires';

    /**
     * @var string
     */
    protected $view = 'comments';

    /**
     * @var bool
     */
    protected $admin_access = true;

    /**
     * @var string
     */
    private $folder = 'admin';

    /**
     * view of action
     *
     * @param array Datas POST|GET
     * @return void
     */
    public function commentsAction(array $datas = array()): void
    {
        $this->init($datas);

        $userModel = new UserModel;
        $postsModel = new PostsModel;
        $this->commentsModel = new CommentsModel;
        $this->page = empty($this->datas_match['page']) ? 1 : (int) $this->datas_match['page'];
        $this->datas['page'] = $this->page;
        $filters = array();

        foreach ($this->datas_post as $key => $post) {
            if (!empty($this->datas_post[$key])) {
                $column = explode('_', $key)[0];
                $filters[$post] = $column;
                $this->datas[$key] = $post;
            }
        }

        $this->getNbrsItems('valider');
        $this->getNbrsItems('refuser');
        $this->getNbrsItems('en_attente');

        $comments = $this->commentsModel->fetchAll(true, 'comment_id', $this->page, $filters, 'DESC');

        if (!empty($comments)) {
            $this->nbrs_page = $this->commentsModel->getNbrsPage();
            $this->disabledPagination();
            $this->datas['nbrs_page'] = $this->nbrs_page;
    
            foreach ($comments as &$comment) {
                $itemUser = $userModel->fetchId($comment->user_id);
                if (!empty($itemUser)) {
                    $comment->pseudo = $itemUser['pseudo'];
                }

                $itemPost = $postsModel->fetchId($comment->post_id);
                if (!empty($itemPost)) {
                    $comment->title_post = $itemPost['title'];
                }

                if ($comment->statut === 'valider') {
                    $comment->color_statut = '#52BE80';
                    $comment->statut = 'Valider';
                } elseif ($comment->statut === 'refuser') {
                    $comment->color_statut = '#EC7063';
                    $comment->statut = 'Refuser';
                } else {
                    $comment->color_statut = '#F5B041';
                    $comment->statut = 'En attente';
                }

                $comment->content = nl2br($comment->content);
            }

            if (!empty($this->datas_get['valide'])) {
                $datas_save['statut'] = 'valider';
                $this->commentsModel->save($datas_save, (int) $this->datas_get['valide']);
                $_SESSION['success'] = 'Commentaire accepté';
                header('Location: /admin/comments/' . $this->page);
                exit();
            }

            if (!empty($this->datas_get['refuse'])) {
                $datas_save['statut'] = 'refuser';
                $this->commentsModel->save($datas_save, $this->datas_get['refuse']);
                $_SESSION['success'] = 'Commentaire refusé';
                header('Location: /admin/comments/' . $this->page);
                exit();
            }

            $this->datas['comments'] = $comments;
        } else {
            $this->datas['errors'] = 'Aucun commentaire trouvé';
        }

        echo $this->viewsRender($this->view, $this->datas, $this->folder);
    }

    /**
     * Count the number of elements
     *
     * @param  string $statut statut of item
     * @return void
     */
    private function getNbrsItems($statut)
    {
        $this->datas['comments_' . $statut] = '+ ' . sizeof($this->commentsModel->getAllWithParams(array(
            'statut' => $statut
        )));
    }
}
