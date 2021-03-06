<?php
include_once('config.php');
include_once('app/projects.php');
include_once('app/users.php');

if($_GET['uid']){
	$user = $_GET['uid'];}
else{
	$user = $_SESSION['uid'];}
	
$interactions = getInteractions($user);


if ($interactions){
	foreach ($interactions as $i){
		$timestamp = $i['created_on'];
		$user = getUserNameById($i['created_by']);
		$author = getUserNameById($i['author']);
		if($i['type'] == 'ideathread'){$title = getIdeaTitle($i['id']); $url = 'home.php?iid='.$i['id'];}
		if($i['type'] == 'project'){$title = getProjectTitle($i['id']); $url = 'home.php?pid='.$i['id'];}
		
		if($i['action'] == 'like'){
			$action = 'liked';
			$icon_image = '/images/icons/like.png';
		}
		if($i['action'] == 'comment'){
			$action = 'commented on';
			$icon_image = '/images/icons/comment.jpg';
		}
		if($i['action'] == 'rate'){
			$action = 'rated';
			$icon_image = '/images/icons/star.jpg';
		}
		if($i['action'] == 'route'){
			$action = 'routed';
			$icon_image = '/images/icons/routeproject.jpg';
		}
		
		?>
		
		<div style="font-size:15px; margin-bottom:10px;" >
        		<img src="<?php echo $icon_image?>" width="20px">
        		<span class=""><?php echo $user.' '.$action.' '?><a href="/user.php?uid=<?php echo $i['author']?>" style="color: #FF4F00; text-decoration:none;"><?php echo $author?></a><?php echo "'s ". $i['type']?> <a href="<?php echo $url?>" style="color: #FF4F00; text-decoration:none;"><?php echo $title?>.<a></span>
        		<span style="margin-left:10px; font-size:13px;"><?php echo TimeAgo(date('Y-m-d', strtotime($timestamp))); ?> </span>
        	</div>
		
<?php	}
	
	}
?>