<?php

require_once('config.php');

$action = $_POST['dispatcher'];


switch ($action) {

    case 'login':
        $user_id = login($_POST['email'], $_POST['password'], $_POST['remember']);

        if ($user_id)
            $responce['result'] = 'OK';
        else
            $responce['result'] = '';
        echo json_encode($responce);

        break;

    case 'add-developer':
        require_once(DIR_APP . 'users.php');
        $id = addDeveloper($_POST);

        if (!empty($id)) {
            $responce['result'] = 'OK';
            $responce['id'] = $id;
        } else
            $responce['result'] = '';

        echo json_encode($responce);
        break;

    case 'rate-project':
        require_once(DIR_APP . 'projects.php');
        require_once(DIR_APP . 'users.php');

        $id = rateProject($_POST['project_id'], $_POST['user_id'], $_POST['value']);

        if ($id) {
            $responce['result'] = 'OK';

            $project_title = getProjectTitle($_POST['project_id']);
            $sent_to = getProjectAuthor($_POST['project_id']);
            $author = getUserNameById($_POST['user_id']);

            $text = $author . ' rated project ' . $project_title;
            addNotification($sent_to, $text, $_POST['user_id']);
            addInteraction($_SESSION['uid'], 'rate', $sent_to, 'project', $_POST['project_id']);
            
        } else
            $responce['result'] = '';

        echo json_encode($responce);
        break;

    case 'route-project':
        require_once(DIR_APP . 'projects.php');
        $id = AddProjectRouter($_POST['project_id'], $_SESSION['uid']);

        if (!empty($id)) {
            $responce['result'] = 'OK';
            $responce['id'] = $id;
        } else
            $responce['result'] = '';

        echo json_encode($responce);
        break;

    case 'remove-route-project':
        require_once(DIR_APP . 'projects.php');
        $id = RemoveProjectRouter($_POST['project_id'], $_SESSION['uid']);

        if (!empty($id)) {
            $responce['result'] = 'OK';
            $responce['id'] = $id;
        } else
            $responce['result'] = '';

        echo json_encode($responce);
        break;


    case 'like-project':
        require_once(DIR_APP . 'projects.php');
        require_once(DIR_APP . 'users.php');
        $id = AddProjectLike($_POST['project_id'], $_SESSION['uid']);

        if (!empty($id)) {
            $likes = getLikes($_POST['project_id']);

            $responce['result'] = 'OK';
            $responce['id'] = $id;
            $responce['likes'] = $likes;

            $project_title = getProjectTitle($_POST['project_id']);
            $sent_to = getProjectAuthor($_POST['project_id']);
            $author = getUserNameById($_SESSION['uid']);

            $text = $author . ' liked project ' . $project_title;
            addNotification($sent_to, $text, $_SESSION['uid']);
            
            addInteraction($_SESSION['uid'], 'like', $sent_to, 'project', $_POST['project_id']);
            
        } else
            $responce['result'] = '';

        echo json_encode($responce);
        break;
        
     case 'like-idea':
        require_once(DIR_APP . 'projects.php');
        require_once(DIR_APP . 'users.php');
        $id = AddIdeaLike($_POST['ideathread_id'], $_SESSION['uid']);
       
        if (!empty($id)) {
            $likes = getIdeaLikes($_POST['ideathread_id']);
            $responce['result'] = 'OK';
            $responce['id'] = $id;
            $responce['likes'] = $likes;

            $ideathread_title = getIdeaTitle($_POST['ideathread_id']);
            $sent_to = getIdeaAuthor($_POST['ideathread_id']);
            $author = getUserNameById($sent_to);
            $user = getUserNameById($_SESSION['uid']);

            $text = $user . ' liked your ideathread ' . $ideathread_title;
            
            addNotification($sent_to, $text, $_SESSION['uid']);
            plusInteraction($_POST['ideathread_id']);
            
           
            
            addInteraction($_SESSION['uid'], 'like', $sent_to, 'ideathread', $_POST['ideathread_id']);
            
        } else
            $responce['result'] = '';

        echo json_encode($responce);
        break;

    case 'remove-like-project':
        require_once(DIR_APP . 'projects.php');
        $id = RemoveProjectLike($_POST['project_id'], $_SESSION['uid']);

        if (!empty($id)) {
            $likes = getLikes($_POST['project_id']);
            $responce['result'] = 'OK';
            $responce['id'] = $id;
            $responce['likes'] = $likes;
        } else
            $responce['result'] = '';

        echo json_encode($responce);
        break;


    case 'remove-like-idea':
        require_once(DIR_APP . 'projects.php');
        $id = RemoveIdeaLike($_POST['ideathread_id'], $_SESSION['uid']);
        minusInteraction($_POST['ideathread_id']);

        if (!empty($id)) {
            $likes = getIdeaLikes($_POST['ideathread_id']);
            $responce['result'] = 'OK';
            $responce['id'] = $id;
            $responce['likes'] = $likes;
        } else
            $responce['result'] = '';

        echo json_encode($responce);
        break;



    case 'reply':
        require_once(DIR_APP . 'users.php');
        $res = sendReply($_POST);

        if ($res) {
            $responce['result'] = 'OK';
        } else
            $responce['result'] = '';

        echo json_encode($responce);

        break;

    case 'add-comment':
        require_once(DIR_APP . 'projects.php');
        require_once(DIR_APP . 'users.php');
        $id = addComment($_POST['project_id'], $_SESSION['uid'], $_POST['text']);



        if (!empty($id)) {
            $responce['result'] = 'OK';
            $responce['id'] = $id;

            $project_title = getProjectTitle($_POST['project_id']);
            $sent_to = getProjectAuthor($_POST['project_id']);
            $author = getUserNameById($_SESSION['uid']);

            $text = $author . ' commented project ' . $project_title;
            addNotification($sent_to, $text, $_SESSION['uid']);
            addInteraction($_SESSION['uid'], 'comment', $sent_to, 'project', $_POST['project_id']);

            $messages = getComments($_POST['project_id']);
            $content = '';
            foreach ($messages as $ix => $m) {
                $content.= '<div class="message-item';
                if (($ix % 2) == 0)
                    $content.= ' odd';
                $content.= '" data-id="' . $ix . '">
 						<div class="message-author">';

                $u = getUserData($m['created_by']);

                $content.= '<div class="router-user-photo">
        				<a href="user.php?uid=' . $u['user_id'] . '">';
                if (empty($u['photo'])) {
                    $content.= '<img src="uploads/avatars/nophoto.jpg" alt="">';
                } else {
                    $content.= '<img src="uploads/avatars/' . $u['photo'] . '" alt="">';
                }
                $content.= '</a>
						<div class="router-user-name">
						<a href="user.php?uid=' . $u['user_id'] . '">' . $u['first_name'] . '<br>' . $u['last_name'] . '</a>
						</div>
						</div>
						<div class="comment-date">' . $m['created_on'] . '</div>
						</div>
					 	<div class="message-content" data-id="' . $ix . '">' . $m['text'] . '</div>';

                if ($m['created_by'] == $_SESSION['uid'])
                    $content.= '<div class="delete delete_' . $m['comment_id'] . '" data-id="' . $m['comment_id'] . '" onclick="deleteComment(\'' . $m['comment_id'] . '\')"></div>';

                $content.= '</div>';
            }

            $responce['content'] = $content;
        } else
            $responce['result'] = '';

        echo json_encode($responce);
        break;
        
    case 'add-idea-comment':
        require_once(DIR_APP . 'projects.php');
        require_once(DIR_APP . 'users.php');
        $id = addIdeaComment($_POST['ideathread_id'], $_SESSION['uid'], $_POST['text']);
        plusInteraction($_POST['ideathread_id']);

        if (!empty($id)) {
            $responce['result'] = 'OK';
            $responce['id'] = $id;

            $ideathread_title = getIdeaTitle($_POST['ideathread_id']);
            $sent_to = getIdeaAuthor($_POST['ideathread_id']);
            $author = getUserNameById($_SESSION['uid']);

            $text = $author . ' commented ideathread ' . $ideathread_title;
            addNotification($sent_to, $text, $_SESSION['uid']);
            addInteraction($_SESSION['uid'], 'comment', $sent_to, 'ideathread', $_POST['ideathread_id']);

            $messages = getIdeaComments($_POST['ideathread_id']);
            
            $content = '';
            foreach ($messages as $ix => $m) {
                $content.= '<div class="message-item';
                if (($ix % 2) == 0)
                    $content.= ' odd';
                $content.= '" data-id="' . $ix . '">
 						<div class="message-author">';

                $u = getUserData($m['created_by']);

                $content.= '<div class="router-user-photo">
        				<a href="user.php?uid=' . $u['user_id'] . '">';
                if (empty($u['photo'])) {
                    $content.= '<img src="uploads/avatars/nophoto.jpg" alt="">';
                } else {
                    $content.= '<img src="uploads/avatars/' . $u['photo'] . '" alt="">';
                }
                $content.= '</a>
						<div class="router-user-name">
						<a href="user.php?uid=' . $u['user_id'] . '">' . $u['first_name'] . '<br>' . $u['last_name'] . '</a>
						</div>
						</div>
						<div class="comment-date">' . $m['created_on'] . '</div>
						</div>
					 	<div class="message-content" data-id="' . $ix . '">' . $m['text'] . '</div>';

                if ($m['created_by'] == $_SESSION['uid'])
                    $content.= '<div class="delete delete_' . $m['comment_id'] . '" data-id="' . $m['comment_id'] . '" onclick="deleteComment(\'' . $m['comment_id'] . '\')"></div>';

                $content.= '</div>';
            }

            $responce['content'] = $content;
        } else
            $responce['result'] = '';

        echo json_encode($responce);
        break;

    case 'report-project':
        require_once(DIR_APP . 'projects.php');
        reportProject($_POST['project_id'], $_POST['copyright'], $_POST['spam'], $_POST['violent'], $_POST['abusive'], $_POST['impersonation'], $_POST['harassment']);

        $responce['result'] = 'OK';

        echo json_encode($responce);
        break;

    case 'delete-comment':
        require_once(DIR_APP . 'projects.php');

        deleteComment($_POST['comment_id']);

        $responce['result'] = 'OK';

        echo json_encode($responce);
        break;
        
    case 'delete-idea-comment':
        require_once(DIR_APP . 'projects.php');

        deleteIdeaComment($_POST['comment_id']);
        minusInteraction($_POST['ideathread_id']);

        $responce['result'] = 'OK';

        echo json_encode($responce);
        break;    
        
    case 'delete-idea':
    	require_once(DIR_APP . 'projects.php');
    	deleteIdea($_POST['ideathread_id']);
    	$response['result'] = 'OK';
    	echo json_encode($response);
    	break;
    
    case 'accept-route':
        require_once(DIR_APP . 'users.php');

        acceptRoute($_POST['routed_by'], $_POST['user_id'], $_POST['notify_id']);

        $responce['result'] = 'OK';

        echo json_encode($responce);
        break;
    
    case 'decline-route':
        require_once(DIR_APP . 'users.php');

        declineRoute($_POST['routed_by'], $_POST['user_id'], $_POST['notify_id']);

        $responce['result'] = 'OK';

        echo json_encode($responce);
        break;
    
    
    case 'share-project':
        require_once(DIR_APP . 'users.php');
        require_once(DIR_APP . 'projects.php');
        
        $routed = $_POST['routed'];
        
        if ($routed) {
            addSuggestion($_POST['project_id'], $_POST['sent_to'], $_SESSION['uid']);
            
            $project_title = getProjectTitle($_POST['project_id']);            
            $author = getUserNameById($_SESSION['uid']);

            $text = $author . ' suggested project ' . $project_title;
            addNotification($_POST['sent_to'], $text, $_SESSION['uid']);
        }
        else
            deleteSuggestion($_POST['project_id'], $_POST['sent_to'], $_SESSION['uid']);
            

        $responce['result'] = 'OK';

        echo json_encode($responce);
        break;
		
	case 'add-transaction':
        require_once(DIR_APP . 'users.php');
        require_once(DIR_APP . 'projects.php');

            $project_id = $_POST['project_id'];
            $project_title = $_POST['project_title'];            
			$user_id = $_SESSION['uid'];
			$author_id = $_POST['created_by'];
			$amount = $_POST['amount'];

            addTransaction($user_id, $amount, $project_title, $project_id, $author_id);

        $responce['result'] = 'OK';

        echo json_encode($responce);
        break;
        
        case 'notifyOwner':
        require_once(DIR_APP . 'users.php');
        require_once(DIR_APP . 'projects.php');

            $project_id = $_POST['project_id'];
            $project_title = $_POST['project_title'];            
			$user_id = $_SESSION['uid'];
			$author_id = $_POST['created_by'];
			$user_name = getUserNameById($_SESSION['uid']);
			
			$text = $user_name. ' wants to view your project '. $project_title;
			
		addNotification($author_id, $text, $user_id);
		
	$responce['result'] = 'OK';

        echo json_encode($responce);
        break;	
        
        
        case 'read-notifications':
        require_once(DIR_APP . 'users.php');
        require_once(DIR_APP . 'projects.php');
        
        $user_id = $_SESSION['uid'];
        readNotifications($user_id);
        
        $responce['result'] = 'OK';
        
        echo json_encode($responce);
        break;
}
?>