<?php
include_once('config.php');
include_once('app/projects.php');
include_once('app/users.php');

//$trend = calculateTrendForProject(6);

$projects = getProjectsInTrend(); //print_r($projects);

if ($projects) {

    foreach ($projects as $pr) {
        
        $project = getProjectById($pr['project_id']);

        $title = $project['project_title'];
        $user = getUserData($project['created_by']);

        if (strlen($title) < 20)
            $short_title = $title;
        else
            $short_title = substr($title, 0, 19) . '...';
        ?>
        <div class="recent-project-item index">

            <?php $image = getFeaturingImage($project['project_id']);
            if (!empty($image)) {
                ?>
                <a href="#" class="recent-project-title index view-more" title="<?php echo $title; ?>"><img src="<?php echo SITE_URL . '/uploads/images/thumbs/' . $image; ?>" alt=""></a>
            <?php } else { ?>
                <a href="#" class="recent-project-title index view-more" title="<?php echo $title; ?>"><img src="<?php echo SITE_URL . '/uploads/avatars/nophoto.jpg'; ?>" alt=""></a>
        <?php } ?>

            <div class="project-bottom-details index">    
            <a href="#" class="recent-project-title index view-more" title="<?php echo $title; ?>"><?php echo $short_title; ?></a>
            <span class="project-rating index"><?php echo calculateRating($project['project_id']); ?></span>
            </div> <!-- project-bottom-details -->

            <div class="project-author index"><?php echo TimeAgo(date('Y-m-d', strtotime($project['created_on']))); ?> by <a href="#" class="view-more"><?php echo $user['display_name']; ?></a></div>

        </div>
    <?php
    } ?>
    
    <div class="recent-project-item index" style="background-color: #ECECEC;">

<div class="see-more index" style="font-size: x-large;    margin: 115px auto;    text-align: center;">

	<a href="#" class="view-more" style="text-decoration: none; color: #615651;">View More</a>
</div></div>;
<?php } else {
	echo '<div class="project-title" style="font-size: 20px; margin-left: 10px;">No projects available right now</div>';
}
?>