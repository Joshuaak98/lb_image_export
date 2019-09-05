<?php
/**
 * Plugin Name: Lichtblick Img Export
 * Plugin URI: http://www.mywebsite.com/my-first-plugin
 * Description: Plugin für den Bildexport im Wordpress Backend
 * Version: 1.0
 * Author: Joshua Kresse
 * Author URI: http://www.mywebsite.com
 */

function custom_js_to_head() {
    $title = the_title();
    $mypost = get_page_by_title( $title, '', 'post' );
    
    
    
    
        ?>
        <script>
        jQuery(function(){
            jQuery("body.post-type-texte .acf-fields").append('<a href="index.php?param=your-action" class="page-title-action"> <?php echo $mypost->ID; ?> </a>');
        });
        </script>
        <?php
    }
    add_action('admin_head', 'custom_js_to_head');




    function custom_img_js_to_head() {

        $page_title = get_the_title();

        ?>
        <script defer>
        jQuery(function(){
            jQuery("body.post-type-bilder .wrap h1").append('<a href="/wp-content/uploads/<?php echo $page_title ?>.zip" class="page-title-action">Bilder Exportieren</a>');
        });

        jQuery(document).ready(function(){

        jQuery(".page-title-action").click(function(){
      
         console.log('<?php do_action("lb_action") ?>')
       })
        })
        </script>
        <?php
       
    }
    add_action('admin_head', 'custom_img_js_to_head');
    
    
    
    add_action( 'restrict_manage_posts', 'add_export_button' );
    function add_export_button() {
        $screen = get_current_screen();
     
        if (isset($screen->parent_file) && ('edit.php?post_type=kontakte' == $screen->parent_file)) {
            ?>
            <input type="submit" name="export_all_posts" id="export_all_posts" class="button button-primary" value="Export All Posts">
            <script type="text/javascript">
                jQuery(function($) {
                    $('#export_all_posts').insertAfter('#post-query-submit');
                });
            </script>
            <?php
        }
    }


    add_action( 'restrict_manage_posts', 'add_img_export_button' );
    function add_img_export_button() {
        $screen = get_current_screen();
     
        if (isset($screen->parent_file) && ('edit.php?post_type=bilder' == $screen->parent_file)) {
            ?>
            <input type="submit" name="export_all_posts" id="export_all_posts" class="button button-primary" value="Export All Posts">
            <script type="text/javascript">
                jQuery(function($) {
                    $('#export_all_posts').insertAfter('#post-query-submit');
                });
            </script>
            <?php
        }
    }
    
    
    
    
    add_action( 'init', 'func_export_all_posts' );
    function func_export_all_posts() {
        if(isset($_GET['export_all_posts'])) {
            $arg = array(
                    'post_type' => 'kontakte',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                );
     
            global $post;
            $arr_post = get_posts($arg);
            if ($arr_post) {
     
                header('Content-type: text/csv');
                header('Content-Disposition: attachment; filename="wp.csv"');
                header('Pragma: no-cache');
                header('Expires: 0');
     
                $file = fopen('php://output', 'w');
     
                fputcsv($file, array('Post Title', 'URL'));
     
                foreach ($arr_post as $post) {
                    setup_postdata($post);
                    fputcsv($file, array(get_the_title(), get_the_permalink()));
                }
     
                exit();
            }
        }
    }
    
global $pagenow;

    
    function my_acf_load_field(){

     
        if( ($pagenow == "post.php") || (get_post_type() == "bilder") ){
    $images = get_field('Galerie', get_the_ID());
    
    if( !empty($images) ){ 
   // print("IZDA");
    
    $result = [];
    
     foreach($images as $image){
    array_push($result, $image["url"]);
     }


     
    
    }
    
    else {
      //  print ("empty");
    }

  $string = json_encode($result);

$output = str_replace("," , " " , $string);
$output_2 = str_replace("[", "", $output);
$output_no_array = str_replace("]", "", $output_2);
$output_no_quotes = str_replace('"', '', $output_no_array);
$output_5 = str_replace('\\', '', $output_no_quotes);

$output_relative = str_replace('https://antjerathje.lichtblickdev.de/', '', $output_5);

       //print $output_relative . "<br>";


        $output_relative_array = explode(" ", $output_relative);

        //print_r($output_relative_array);

       $cdData = strpos($output_no_quotes,"loads\/");

       //print $cdData;

       $myString = preg_replace("/(\d{4,})/", "---", $output_relative);

       $mystring = "";

       foreach($output_relative_array as $arr){
       // echo $arr . " ";
       //echo  substr($arr,0,  27) . " ";

    $imgpath = substr($arr,0,  27);
        $imgname = substr($arr, 27);

     

        $uploadPos = strrpos($imgpath, "uploads/");
        $uploadInt = strval($uploadPos);
        $pathAbUploads = substr($imgpath, $uploadInt + 8);

       

        $fullPath = $pathAbUploads . "" . $imgname . " ";

        $mystring .= $fullPath . " ";

       

        
       }


       $zip_filename = get_the_title();


       //echo $zip_filename;
       

       $shellEx =  "zip " . $zip_filename . ".zip " . $mystring;

    //   print("cd ../wp-content/uploads/ ; rm " . $zip_filename . ".zip ;" . $shellEx . "; pwd");
    


       $myShellRes = shell_exec("cd ../wp-content/uploads/ ; rm " . $zip_filename . ".zip ; ls;" . $shellEx . "; pwd");



     


     //var_dump($myShellRes);

         
  


   }
}




    
    

    
  add_action('lb_action', 'my_acf_load_field');
    
  function hey(){
      return the_title();
  }

   

print_r(hey());


    ?>
    
    
    