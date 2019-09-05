<?php
/**
 * Plugin Name: Lichtblick Img Export
 * Plugin URI: http://www.mywebsite.com/my-first-plugin
 * Description: Plugin für den Bildexport im Wordpress Backend
 * Version: 1.0
 * Author: Joshua Kresse
 * Author URI: http://www.mywebsite.com
 */



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

  
/*
Funktion die alle Bilder in dem "Galarie Feld" der Bilder Beiträge in einer .zip Datei sammelt, und diese im wp-content/uploads Ordner speichert.
*/
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


$string = json_encode($result);

$output = str_replace("," , " " , $string);
$output_2 = str_replace("[", "", $output);
$output_no_array = str_replace("]", "", $output_2);
$output_no_quotes = str_replace('"', '', $output_no_array);
$output_5 = str_replace('\\', '', $output_no_quotes);

$output_relative = str_replace('https://antjerathje.lichtblickdev.de/', '', $output_5);

        $output_relative_array = explode(" ", $output_relative);
        $cdData = strpos($output_no_quotes,"loads\/");
        $myString = preg_replace("/(\d{4,})/", "---", $output_relative);
        $mystring = "";

       foreach($output_relative_array as $arr){
        $imgpath = substr($arr,0,  27);
        $imgname = substr($arr, 27);
        $uploadPos = strrpos($imgpath, "uploads/");
        $uploadInt = strval($uploadPos);
        $pathAbUploads = substr($imgpath, $uploadInt + 8);
        $fullPath = $pathAbUploads . "" . $imgname . " ";
        $mystring .= $fullPath . " "; 
       }
    
       $zip_filename = get_the_title();
       $shellEx =  "zip " . $zip_filename . ".zip " . $mystring;

    


        $myShellRes = shell_exec("cd ../wp-content/uploads/ ; rm " . $zip_filename . ".zip ; ls;" . $shellEx . "; pwd");

   }
}
    
        add_action('lb_action', 'my_acf_load_field');
    
    ?>
    
    
    