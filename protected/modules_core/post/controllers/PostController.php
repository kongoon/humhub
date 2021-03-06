<?php

/**
 * @package humhub.modules_core.post.controllers
 * @since 0.5
 */
class PostController extends Controller
{

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionPost()
    {

        $this->forcePostRequest();
        $_POST = Yii::app()->input->stripClean($_POST);

        $post = new Post();
        $post->content->populateByForm();
        $post->message = Yii::app()->request->getParam('message');

        if ($post->validate()) {

            $post->save();

            // Experimental: Auto attach found images urls in message as files
            if (isset(Yii::app()->params['attachFilesByUrlsToContent']) && Yii::app()->params['attachFilesByUrlsToContent'] == true) {
                File::attachFilesByUrlsToContent($post, $post->message);
            }

            $this->renderJson(array('wallEntryId' => $post->content->getFirstWallEntryId()));
        } else {
            $this->renderJson(array('errors' => $post->getErrors()), false);
        }
    }

}
