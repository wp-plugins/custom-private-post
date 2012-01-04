<?php
/**
 * @package custom-private-post
 */
/*
Plugin Name: Custom Private Post
Plugin URI: http://mengzhuo.org/lab/wordpress/custom-private-post.html
Description: Make your private Post customizable even in RSS feed
Version: 1.0
Author: Meng Zhuo
Author URI: http://mengzhuo.org
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
//definition goes here...
define('CPP','custom-private-post');
define('CPPVERSION','1.0');
//definition end.

load_plugin_textdomain(CPP, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');

// Make sure we don't expose any info if called directly [FROM AKISMET]
if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}
class CPP_SetObj{
    //Show time!
    function __construct(){
        $this->title = __('Something about [title_short]',CPP);

        $this->content = '<p>'.__('Well, [post_author] posted in monkey-tongue, try to read it if you can',CPP)."</p><p><img width='400' src='".plugins_url( 'private.jpg' , __FILE__ )."'></p>";
        
        $this->complete_block = FALSE;
    }
    
}
class CPP {

    function __construct(){

        $this->get_option();
        
        add_action('plugins_loaded', array($this, 'init'));
        
    }
    
    function init(){
    
        add_action( 'the_post',array(&$this,'the_post_action') );
        //add_action( 'atom_head',array(&$this,'feed_head_action') );
        //add_action( 'rss_head',array(&$this,'feed_head_action') );
        //add_action( 'rss2_head',array(&$this,'feed_head_action') );
        
        
        if (current_user_can('administrator')){
            add_action( 'admin_menu',array(&$this,'admin_menu') );
        }
    }
    /*
    function feed_head_action(){
        add_filter( 'the_title_rss',array(&$this,'feed_filter'),1,1 );
        //add_filter( 'the_excerpt_rss',array(&$this,'feed_filter'),1,1);
    }
    
    function feed_filter($what){
        if (get_post_status == 'private')
            return ($what.'ABCDEFGHIJKL'.$which);
    }
    */
    
    function the_post_action(){
    
        add_filter( 'the_title',array(&$this,'title_filter'),0,1 );
        add_filter( 'the_content',array(&$this,'content_filter'),0,1 );
    }
    
    function admin_menu(){
        
        if (current_user_can('administrator')){
            
            add_options_page( __('Custom Private Post',CPP),
                              __('Custom Private Post',CPP),
                              'manage_options',
                              CPP,array(&$this,'admin_page') );
         }
         
    }
    
    function admin_page(){
    
        if ( isset($_GET['back_to_default']) ){
            $this->set_default_option();
        }
    
        if ( isset($_POST['submit']) ){

            $update_setting_filters = array(
                'title' => FILTER_SANITIZE_SPECIAL_CHARS,
                'content' => FILTER_FLAG_STRIP_LOW,
                'complete_block'=>FILTER_SANITIZE_SPECIAL_CHARS );
                
            $update_setting = filter_var_array( $_POST, $update_setting_filters);
                        
            $update_setting['complete_block'] = $this->checkbox_to_boolen( $update_setting['complete_block'] ); //hmm...inefficiency
            
            foreach( $update_setting as $key => $val ){
                $this->option->$key = $val;
            }
            
            update_option(CPP,$this->option);
             
         }

        include_once ( dirname( __FILE__ ) . '/admin_form.php' );
        
    }
    
    function content_filter($content){
        
        if (get_post_status() =='private' && is_object($this->post) ){
                       
            $content_filter = array(
            
                '[post_author]'=>$this->author->display_name,
                '[post_content]'=>$this->post->post_content,
                '[post_date]'=>$this->post->post_date,
                '[post_content_short]'=>substr($this->post->post_content,0,100).'...'
            );
            
            $content = stripcslashes( $this->array_str_replace($content_filter,$this->option->content) );
            
        }
        
        return $content;
        
    }
    
    function title_filter($title){
        
        if ($this->option->complete_block && get_post_status() =='private'){
            
            the_post();//Soooo Easy, but hard to found!
            
            if (is_single()){
                wp_redirect('404');
            }
        }
        
        if (get_post_status() =='private'){
            
            $title = $this->option->title;

            global $post,$authordata;
            
            $this->post = $post;
            $this->author =  $authordata;         

            $title_filter = array(
                '[title]'=>stripcslashes($this->post->post_title),
                '[title_short]'=>stripcslashes(substr($this->post->post_title,0,10).'...'));
        }
        
        return $this->array_str_replace($title_filter,$title);
    }
    
    function array_str_replace($filter,$string){
    
       if ( is_array($filter) && is_string($string) ){
       
         foreach ($filter  as $key => $val){
            if ( substr_count($string,$key) >= 1 )
                $string = str_replace($key,$val,$string);
         }
         
       }
       
       return $string;
    }

    function get_option(){
    
        $this->option = get_option(CPP);
        
        if (!is_object($this->option)){
            $this->set_default_option();
        }
    }
    function checkbox_to_boolen($input){
    
        return ($input == 'on')?TRUE:FALSE;
    
    }
    
    function set_default_option(){
        
        $default_option = new CPP_SetObj;  
        
        if (!is_object($this->option)){
            
            if (get_option(CPP)){
                
                remove_option(CPP);
            }
            
            add_option(CPP,$default_option,'','no');
        }
        
        $this->option = $default_option;
    }
}
new CPP;
