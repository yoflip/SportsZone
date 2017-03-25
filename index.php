<?php
//This page displays the list of the forum's categories
include('config.php');

global $con;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <style>

ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
    border: 1px solid #e7e7e7;
    background-color: #f3f3f3;
}

li {
    float: left;
}

li a {
    display: block;
    color: #666;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
}
</style>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="<?php echo $design; ?>/style.css" rel="stylesheet" title="Style" />
        <title>SportsZone</title>
    </head>

    <body>
    	<div class="header">
        	<a href="<?php echo $url_home; ?>"><img src="<?php echo $design; ?>/images/logo.png" alt="Forum" /></a>
	    </div>
        <div>
            <ul>
                <li><a class="active" href="index.php">Home</a></li>
		<li><a href="index.php">Statistics</a></li>
		<li><a href="index.php">Forum</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="signup.php">Signup</a></li>
                
            </ul>
        </div>
        <div class="content">


        <?php 

                //http://api.football-data.org/v1/competitions/398/leagueTable

                $login = 'X-Auth-Token'; //api auth user
                $password = 'b2c96b2a5c8d46e6814cc15ecffa2c35'; //this api auth key
                $url = 'http://api.football-data.org/v1/competitions/?season=2016'; // this is url of service provider


                if (isset($_GET['league']) && !empty($_GET['league'])) 
                {
                    
                    $url = "http://api.football-data.org/v1/competitions/". $_GET['league'] ."/leagueTable";
                    //http://api.football-data.org/v1/competitions/398/leagueTable
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
                $result = curl_exec($ch);
                $result = json_decode($result, true);
                curl_close($ch);  

                // echo "<pre>"; print_r($result); echo "</pre>";
                
                if (isset($result) && count($result) > 0) 
                {
                    
                    if (isset($_GET['league']) && !empty($_GET['league'])) 
                    {
                ?>
                        <h3><?php echo $result['leagueCaption'] ?></h3>
                        <h4>Matches: <?php echo $result['matchday'] ?></h4>
                        <table class="categories_table">                    
                        <tr>
                            <th class="forum_cat">Group</th>
                            <th class="forum_nrep">Team</th>
                            <th class="forum_nrep">Rank</th>
                            <th class="forum_nrep">Play Games</th>
                            <th class="forum_nrep">Points</th>
                            <th class="forum_nrep">Goals</th>
                        </tr>
                        <?php 

                            if (isset($result['standings']) and count($result['standings']) > 0) 
                            {
                            
                                foreach ($result['standings'] as $group => $teams) 
                                {

                                    foreach ($teams as $key => $team) {
                                        
                                    ?>

                                    <tr>
                                        <td><?php echo $group; ?></td>
                                        <td><?php echo $team['team']?></td>
                                        <td><?php echo $team['rank']?></td>
                                        <td><?php echo $team['playedGames']?></td>
                                        <td><?php echo $team['points']?></td>
                                        <td><?php echo $team['goals']?></td>
                                    </tr>

                         <?php
                                    }
                                }
                            }
                         ?>
                    </table>
                    <br>
                    <br>                

                <?php
                    }
                    else {
         ?>

                    <table class="categories_table">                    
                        <tr>
                            <th class="forum_cat">Tournament</th>
                            <th class="forum_nrep">Matches</th>
                            <th class="forum_nrep">Teams</th>
                        </tr>
                        <?php 

                            foreach ($result as $key => $value) 
                            {
                                ?>

                                <tr>
                                    <td><a href="index.php?league=<?php echo $value['id']; ?>" ><?php echo $value['caption']; ?></a></td>
                                    <td><?php echo $value['numberOfGames']?></td>
                                    <td><?php echo $value['numberOfTeams']?></td>
                                </tr>

                                <?php
                            }

                         ?>
                    </table>
                    <br>
                    <br>
<?php
                    }
                }


if(isset($_SESSION['username']))
{
$nb_new_pm = mysqli_num_rows(mysqli_query($con, 'select count(*) as nb_new_pm from pm where ((user1="'.$_SESSION['userid'].'" and user1read="no") or (user2="'.$_SESSION['userid'].'" and user2read="no")) and id2="1"'));
$nb_new_pm = $nb_new_pm['nb_new_pm'];
?>

<?php
}
else
{
?>

<?php
}
if(isset($_SESSION['username']) and $_SESSION['username']==$admin)
{
?>
	<a href="new_category.php" class="button">New Category</a>
<?php
}
?>
<table class="categories_table">
	<tr>
    	<th class="forum_cat">Category</th>
    	<th class="forum_ntop">Topics</th>
    	<th class="forum_nrep">Replies</th>
<?php
if(isset($_SESSION['username']) and $_SESSION['username']==$admin)
{
?>
    	<th class="forum_act">Action</th>
<?php
}
?>
	</tr>
<?php

$dn1 = mysqli_query($con, 'select c.id, c.name, c.description, c.position, (select count(t.id) from topics as t where t.parent=c.id and t.id2=1) as topics, (select count(t2.id) from topics as t2 where t2.parent=c.id and t2.id2!=1) as replies from categories as c group by c.id order by c.position asc');

$nb_cats = mysqli_num_rows($dn1);
while($dnn1 = mysqli_fetch_array($dn1))
{
?>
	<tr>
    	<td class="forum_cat"><a href="list_topics.php?parent=<?php echo $dnn1['id']; ?>" class="title"><?php echo htmlentities($dnn1['name'], ENT_QUOTES, 'UTF-8'); ?></a>
        <div class="description"><?php echo $dnn1['description']; ?></div></td>
    	<td><?php echo $dnn1['topics']; ?></td>
    	<td><?php echo $dnn1['replies']; ?></td>
<?php
if(isset($_SESSION['username']) and $_SESSION['username']==$admin)
{
?>
    	<td><a href="delete_category.php?id=<?php echo $dnn1['id']; ?>"><img src="<?php echo $design; ?>/images/delete.png" alt="Delete" /></a>
		<?php if($dnn1['position']>1){ ?><a href="move_category.php?action=up&id=<?php echo $dnn1['id']; ?>"><img src="<?php echo $design; ?>/images/up.png" alt="Move Up" /></a><?php } ?>
		<?php if($dnn1['position']<$nb_cats){ ?><a href="move_category.php?action=down&id=<?php echo $dnn1['id']; ?>"><img src="<?php echo $design; ?>/images/down.png" alt="Move Down" /></a><?php } ?>
		<a href="edit_category.php?id=<?php echo $dnn1['id']; ?>"><img src="<?php echo $design; ?>/images/edit.png" alt="Edit" /></a></td>
<?php
}
?>
    </tr>
<?php
}
?>
</table>
<?php
if(isset($_SESSION['username']) and $_SESSION['username']==$admin)
{
?>
	<a href="new_category.php" class="button">New Category</a>
<?php
}
if(!isset($_SESSION['username']))
{
?>

<?php
}
?>
		</div>		
	</body>
</html>
