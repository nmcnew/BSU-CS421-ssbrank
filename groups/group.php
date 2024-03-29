<?php
    ob_start();
    $path = $_SERVER['DOCUMENT_ROOT'];
    $path .= "/header.php";
    include_once($path);
    include_once("groupHeader.php");
    $buffer=ob_get_contents();
    ob_end_clean();

    $title = clean($_GET['name']);
    $buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $buffer);

    echo $buffer;
?>
	
    
    <?php
    $db = PDOFactory::getConnection();
    $stmt = $db->prepare('SELECT * from community where name = ?');
    $stmt->execute([$_GET['name']]);
    $info = $stmt->fetch();
    $users = get_users($_GET['name']);
    echo '<div id="group-desc">';
    echo '<h1>' . $info['name'] .'</h1>';
    if(is_community_user($info['id'], $_SESSION['user_id'])){
        echo '<span><input type="button" class="leave" id="join-button" onClick="join()" value="Leave Group"></span>';
    }
    else echo '<span><input type="button" id="join-button" onClick="join()" value="Join Group"></span>';
    echo '<div class="header" style="background-image:url(' . $info['header_image'] . ')"></div>';
    echo '<p>' . $info['description'] . '</p>';
    echo '<h4>Members</h4>';
    echo '<div class="user-images">';
    #echo '<pre>'. print_r($users,true) . '</pre>';
    if(!empty($users)){
        foreach($users as $u){
            $img = get_gravatar($u['email'], 30);
            echo '<a href="/users/user.php?name=' . $u['name'] . '">'; 
            echo '<img src="' . $img . '">';
            echo '</a>';
        }
    }
    echo '</div>';
    echo '</div>';
    ?>

    <div id='post-box'>
            <div class="poster-info">
                <img class="poster-image" src="<?php echo get_gravatar_from_id($_SESSION['user_id']) ?>" />
                <a href="/groups/group.php?name=" class="post-location"></a>
                <a href="/users/user.php?name=" class="poster-name"></a>
            </div>
        <form id="postform">
            <textarea class="post-text" name="post-text" rows="4" placeholder="What's up?"></textarea>
            <input type="submit"><!--need to style-->
        </form>
    </div>
    <div id="posts">
    <?php
    $posts = PDOFactory::getGroupPosts($_GET['name']);
    echo formatPosts($posts);
    ?>
    </div>
    </div>
<script>
//TODO: add a polling doo-dad to allow more application like experience?
    $('#postform').submit(function(event){
        event.preventDefault();
        var postinfo = $('#postform').serializeArray();
        console.log(postinfo);
        $.ajax({
            type:'POST',
            url: 'groupActions.php',
            data: {
                action: 'new_post',
                post_text: postinfo[0]['value'],
                group_name: '<?php echo $info['name']; ?>'
            },
            dataType: 'Text',
            success: function(response){
                console.log(response);
                $('#postform')[0].reset();
                $('#posts');
            },
            error: function(response){
                alert("Error posting post: " + response);
                
            }

        });
    });
    function join(){
        $('#join-button').prop('disabled', true);
        if($('#join-button').hasClass('leave')){
            var saveData = $.ajax({
                type:'POST',
                url: 'groupActions.php',
                data: {
                    action: 'Leave',
                    group_name: '<?php echo $info['name']; ?>'
                },
                dataType: 'Text',
                success: function(response){
                    $('#join-button').attr('value','Join Group');
                    $('#group-desc').append(response);
                    $('#join-button').removeClass('leave');
                },
                error: function(){
                    alert('Error Leaving');

                }
            });


        }
        else{
            $.ajax({
                type:'POST',
                url: 'groupActions.php',
                data: {
                    action: 'Join',
                    group_name: '<?php echo $info['name']; ?>'
                },
                dataType: 'Text',
                success: function(response){
                    $('#join-button').attr('value','Leave Group');
                    $('#join-button').addClass('leave');
                },
                error: function(response){
                    alert('ERROR JOINING');
                    $('#group-desc').append(response);

                }
            });

        }
        $('#join-button').prop('disabled', false);
    }
</script>
<?php
    $path = $_SERVER['DOCUMENT_ROOT'];
    $path .= "/footer.php";
	include_once($path);
?>