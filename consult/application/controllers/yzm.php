<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Yzm
 */
class Yzm extends MY_Controller {

    public function __construct() {
        parent::__construct(false);
        $this->load->library('Securimage');
    }

    public function authcode_gen_img() {
        $img = new Securimage();

        // Change some settings
        $img->ttf_file = FCPATH.'/yzm/authcodefont.ttf';
        $img->signature_font = FCPATH.'/yzm/authcodefont.ttf';
        $img->gd_font_file  = FCPATH.'/yzm/authcodefont.ttf';

        $img->code_length = 5;
        $img->image_width = 200;
        $img->image_height = 80;
        $img->perturbation = 0.7;

        // 1.0 = high distortion, higher numbers = more distortion
        //$img->image_bg_color = new Securimage_Color("#0099CC");
        //$img->text_color = new Securimage_Color("#FF6600");
        /*$img->multi_text_color = array(new Securimage_Color("#3399ff"),
        new Securimage_Color("#3300cc"),
        new Securimage_Color("#3333cc"),
        new Securimage_Color("#6666ff"),
        new Securimage_Color("#99cccc")
        );
        $img->use_multi_text = true;*/
        //$img->text_transparency_percentage = 65; // 100 = completely transparent

        $img->num_lines = 5;
        $img->line_color = new Securimage_Color("#999999");
        $img->signature_color = new Securimage_Color(rand(0, 64), rand(64, 128), rand(128, 255));
        $img->image_type = SI_IMAGE_PNG;


        $img->show(); // alternate use:  $img->show('/path/to/background_image.jpg');

    }

    public function authcode_check_code($code) {
        $img = new Securimage();
        $valid = $img->check($code);
        return $valid;
    }
}