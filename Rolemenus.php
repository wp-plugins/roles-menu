<?php 
/**
 * Plugin Name: Role Menus  
 * Plugin URI: 
 * Description: Add and display role based menu.
 * Version: 1.01.0
 * Author: 
 * Author URI: http://www.bgiplc.com/
 * License: A short license name. Example: GPL2
 */

define('ROLE_M_URL', plugins_url('', __FILE__ ) . '/');


function rm_admin_actions() {
   add_menu_page("RolesMenus", "RolesMenus", "publish_posts", "RolesMenus", "roles_menus");

 }

add_action('admin_menu', 'rm_admin_actions');
add_action( 'admin_enqueue_scripts','rlm_styles' );


function rlm_styles(){
wp_enqueue_style('wppm_adminstyle', ROLE_M_URL .'rlm.css');

}

function roles_menus(){
 
   if(isset($_POST['submit-rlm'])){

   // print_r($_POST);

    foreach ($_POST as $key => $value) {
       $data =  sanitize_text_field($value);
       update_option($key,$data);
      # code...
    }

   $saved ='<div class="updated " id="message">
           <p>Settings saved.</p></div>';

        echo $saved;
   
   }

  ?>
     <form  action="" method="post">
    <table class="rlm-table">
    <tr><td colspan="2"><b><label>Role Menu Settings </label></b></td></tr>
    <tr> <td><label>Header Menu (Loggedin User)</label></td>
    <td><?php echo list_menus("loginusrmenu") ?></td></tr>

      <tr><td><label>Header Menu (Log out user)</label></td>
      <td><?php echo list_menus("logoutmenu") ?></td></tr>

      <tr><td colspan="2"><label><b>Role Based Menus</b> </label></td></tr>
      <tr><td><label>Use Role based Menu </label>
       </td>
       <?php 
       // echo get_option('userlm');
       $checkval = (get_option('userlm')=='on') ? "checked" : " "  ?>
       <td><input <?php echo $checkval ?> type="checkbox"  name="userlm" />
        
       </td></tr>
      
       <tr><td><label>Role </label>
       </td>
       <td><label> Custom Menu </label></td></tr>
      
 <?php
         //echo get_current_user_role ();
         $editable_roles = array_reverse( get_editable_roles() );

        foreach ( $editable_roles as $role => $details ) {
                 $name = translate_user_role($details['name'] );
                  //echo "$name <br>";
?>

       <tr><td><label><?php echo $name ?></label>
       </td>
       <td><?php echo list_menus($name) ?></td></tr>

    <?php   

         }
  
   ?>
      <tr><td colspan="2"><input class="button-primary" type="submit" value="Submit" name="submit-rlm" /></td>
      </tr>
      </table>
      </form>


	<?php 
}


 function list_menus($name="",$id=""){
   $menus =  Get_All_Wordpress_Menus();

   if(empty($menus)){
   echo "No Menus were added.";
   return;

   }

  $output = '<select id="'.$id.'" name="'.$name.'">';

  foreach ($menus as $menu) {
    $seleted = ($menu->name==get_option($name)) ? "selected "  : " ";
   
  	$output.='<option '.$seleted.' value="'.$menu->name.'">'.$menu->name.'</option>';
   }
   $output.='</select>';
   return $output;

 }


function Get_All_Wordpress_Menus(){
    return get_terms( 'nav_menu', array( 'hide_empty' => true ) ); 
}

//add_filter( 'wp_nav_menu', 'rlm_header_menu', 1, 1 );

function rlm_header_menu(){

  $items = "testhis";
   return $items;
}


function show_rlm_menus($class_name=""){
 // wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu' ) );
 
  if(is_user_logged_in()){
  
         if(get_option('userlm')=='on'){
      $user_role = get_current_user_role ();
      $Role = ucfirst($user_role);



      wp_nav_menu( array('menu' => get_option($Role),'menu_class' =>$class_name));
      }else{

         wp_nav_menu( array('menu' => get_option('loginusrmenu') ,'menu_class' =>$class_name));
      }

  }else{

      wp_nav_menu( array('menu' => get_option('logoutmenu'),'menu_class' =>$class_name));
      }
}





  function get_current_user_role () {
    global $current_user;
    get_currentuserinfo();
    $user_roles = $current_user->roles;
    $user_role = array_shift($user_roles);
    return $user_role;
   }



add_action('wp_ajax_nopriv_get_topmenu', 'get_topmenu');
add_action('wp_ajax_get_topmenu', 'get_topmenu');

   function get_topmenu(){

  if(is_user_logged_in()){
   // echo "yessssssssss";
    //get_option('loginusrmenu');

    wp_nav_menu( array('menu' => get_option('loginusrmenu')));

      /*if(get_option('userlm')=='on'){
      $user_role = get_current_user_role ();
      $Role = ucfirst($user_role);
      wp_nav_menu( array('menu' => get_option($Role)));
      }else{

         wp_nav_menu( array('menu' => get_option('loginusrmenu')));
      }*/

  }else{

      wp_nav_menu( array('menu' => get_option('logoutmenu')));
      }
      die();

}


?>
