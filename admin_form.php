<?php if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}?>

<div class="wrap">
   <div id="icon-options-general" class="icon32"></div>
   <h2><?php _e('Custom Private Post',CPP); ?></h2>
    <form method='post' action=''>
    <div class="metabox-holder has-right-sidebar" id="poststuff">
    <div class="inner-sidebar"  >
    <div class="postbox">
    <h3 class="hndle"><span><?php _e('Settings');?></span></h3>
    <div class="misc-pub-section">
        <p class="meta-options inside">
        <label for="complete_block"><input type="checkbox" <?php checked($this->option->complete_block, TRUE); ?> accesskey="b" id="complete_block" name="complete_block"></input><?php _e("Don't show the Private post anywhere",CPP); ?></label>
        </p>
    </div>
    <p id="major-publishing-actions">
        <span class="preview button" id="back_default" tabindex="4" style="float:left;"><?php _e( 'Default' );?></span>
        <input type="submit" name="submit" id="publish" class="button-primary" value="<?php _e( 'Save' );?>" tabindex="5" accesskey="p" style="float:right;"/>
    <div class="clear"></div>
    </p>
    
    </div>
    <div class="update-nag" style="border-top:1px #E6DB55 solid; margin:1em 0;text-align:left;padding:0 1.5em"><h3 style="text-align:center"><?php _e('Notice',CPP) ?></h3>
    <p>
    <?php _e('You can use the tags blow for replacement',CPP);?>
    <?php printf(__('<ul><li>[%s]: Post title<li>[%s]: Shorten post title</ul>',CPP),'title','title_short');?>
    <hr/>
    <?php printf(__('<ul><li>[%s]: Post Author<li>[%s]: Post Date<li>[%s]: Shorten post content</ul>',CPP),'post_author','post_date','post_content_short');?>
    </p>
    </div>
    </div>
    <div id="post-body-content">
    <div id="titlediv">
        <div id="titlewrap">
	        <label for="title" id="title-prompt-text" class="hide-if-no-js"><?php _e( 'Enter title here' )?></label>
	        <input type="text" autocomplete="off" id="title" value="<?php echo $this->option->title;?>" tabindex="1" size="30" name="title" />
        </div>
    </div>

<?php
$processed_content = stripcslashes($this->option->content);
if ( function_exists('the_editor') ){
    //for WP< 3.3
    the_editor($processed_content,
              'content',
              '',FALSE);
        
}
elseif ( function_exists ('wp_editor') ){
    //For WP >= 3.3
    wp_editor($processed_content,
              'content',
              array('media_buttons'=>FALSE));
}
else{ 
    //for unknown
?>
<div id='editorcontainer'><textarea rows='10' cols='40' name='content' tabindex='2' id='content'><?php echo $processed_content;?></textarea></div>
<?php }?>

    </div>
</div>
</form>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
    $ = jQuery;
    if ($('#title').val() != ""){
        $('#title').prev().hide(0);
    }
    $("#title").click(function (){
        $(this).prev().hide(0);
        _self = $(this)
        $(this).change(function (){
            if (_self.val() == ""){
                _self.prev().show(0);
            }
        })
    });
    
    $('#back_default').click(function(){
        if (confirm(" <?php _e('Back to default setting?');?> ")){
            window.location.href = "<?php menu_page_url( CPP, 1 );?>&back_to_default=1";
        }
    })
});
</script>
