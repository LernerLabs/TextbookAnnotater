<?php

#$file1 =  "vendor/autoload.php";
#require_once $file1;

require_once plugin_dir_path( __FILE__ ) . 'src/PHPPresentation/src/PhpPresentation/Autoloader.php';
\PhpOffice\PhpPresentation\Autoloader::register();
require_once plugin_dir_path( __FILE__ ) . 'src/Common/src/Common/Autoloader.php';
\PhpOffice\Common\Autoloader::register();

use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Shape\Drawing;

# maybe make a function that takes a student response ID, gives you a file to download.
# write two functions. One takes a single student response ID and gets it from the DB and makes the PPTX
# another takes a textbook ID.

function generate_single_response_slide($currentSlide, $oPHPPresentation, $img_url, $scientist_name, $description){
    // API change: eventually, this gets called from a form, and so we don't bundle
    // everything up as a response. We call iwth the individual bits of data we need.
    # TODO: FIXME:
    # we used to do this when it was a file.
    #$shape = $currentSlide->createDrawingShape();
    #$image_filename = plugin_dir_path( __FILE__ ) . "Noether.jpg";
    #          ->setPath($image_filename)
    # The below code runs, but doesn't work. And, even if it did work, it would
    # only work if we had a JPEG. Need a more general version.

    //$img_url ="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e5/Noether.jpg/220px-Noether.jpg";
    // https://www.opentooleveryone.com/blog/phpoffice-phppresentation-creating-the-images-slide-dynamically-getting-the-source-from-some-db
    $imageData = "data:image/jpeg;base64,".base64_encode(file_get_contents($img_url));
    #list($width, $height) = getimagesize($img_url);
    $shape = new Drawing\Base64();
    # We'll assume about 96 pixels per inch.
    $shape->setName('Scientist image')
          ->setDescription('Scientist image')
          ->setData($imageData)
          ->setResizeProportional(true)
          ->setHeight(5*96)
          ->setOffsetX(0.5*96)
          ->setOffsetY(0.5*96);
	# Give it a drop shadow
	$shape->getShadow()->setVisible(true)
          ->setDirection(45)
          ->setDistance(10);
    # And the text
	$shape = $currentSlide->createRichTextShape()
                          ->setHeight(300)
                          ->setWidth(600)
                          ->setOffsetX(96*3)
                          ->setOffsetY(180);
	$shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
	$textRun = $shape->createTextRun($scientist_name);
	$textRun->getFont()->setBold(true)
            ->setSize(60)
            ->setColor( new Color('FFE06B20'));
	# And the instructor notes
	$oNote = $currentSlide->getNote();
	$oProperties = $oPHPPresentation->getPresentationProperties();
	$oProperties->setCommentVisible(true);
    
	$oRichText = $oNote->createRichTextShape()
	                   ->setHeight(60)
                       ->setWidth(60)
                       ->setOffsetX(170)
                       ->setOffsetY(180);
	$oRichText->createTextRun('Description of Scientist:');
	$oRichText->createParagraph()->createTextRun($description);
}

function generate_single_slide_presentation($img_url, $scientist_name, $description){
    # Now make the PPTX files
	$oPHPPresentation = new PhpPresentation();
	$currentSlide = $oPHPPresentation->getActiveSlide();
	generate_single_response_slide($currentSlide, $oPHPPresentation, $img_url, $scientist_name, $description);
    return $oPHPPresentation;
}

function generate_and_download_slide($img_url, $scientist_name, $description){
    // This gets called from a form, so it doesn't take in a response. It takes in the parts of one.
    $img_url = "https://upload.wikimedia.org/wikipedia/commons/thumb/e/e5/Noether.jpg/220px-Noether.jpg";
    $oPHPPresentation = generate_single_slide_presentation($img_url, $scientist_name, $description);
    get_presentation_as_php($oPHPPresentation);
}

function get_presentation_as_php($oPHPPresentation){
    // https://stackoverflow.com/questions/8566196/phpexcel-to-download
    $oWriterPPTX = IOFactory::createWriter($oPHPPresentation, 'PowerPoint2007', $download=true);
    
    header("Content-Description: File Transfer");
    header("Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation");
    header("Content-Disposition: attachment; filename=test.pptx"); // could fix this
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Expires: 0');
    $oWriterPPTX->save('php://output');

}

function put_presentation_in_database_and_get_url($oPHPPresentation){
    // This should make use of some built in method that returns the
    // presentation as a blob. I don't see that method, though I could
    // be missing something obvious.  So, we dump to disk and read
    // in. Not pretty. But worky.
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );
    
    $oWriterPPTX = IOFactory::createWriter($oPHPPresentation, 'PowerPoint2007');
    $outfname = __DIR__ . "/" . "presentation_to_delete.pptx";
    $oWriterPPTX->save($outfname);
    // updated thinking:
    // after this, use wp_redirect() to point at that file, let them download it, then delete it.

    // not media_handle_upload.
    // https://stackoverflow.com/questions/36548735/copy-image-to-my-server-direct-from-url-and-upload-it-to-wordpress-uploads-folde
    $wp_filetype = wp_check_filetype(basename($outfname), null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => 'Noether.jpg',
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment( $attachment, $outfname );
    $imagenew = get_post( $attach_id );
    $fullsizepath = get_attached_file( $imagenew->ID );
    $attach_data = wp_generate_attachment_metadata( $attach_id, $fullsizepath );
    wp_update_attachment_metadata( $attach_id, $attach_data );     

    $pptx_url = wp_get_attachment_url($attach_id);
    return $pptx_url;

}

function generate_textbook_slide_deck($textbook_id){
    $all_student_responses = get_all_student_responses();
    $oPHPPresentation = new PhpPresentation();
    foreach($all_student_responses as $response){
        $currentSlide = $oPHPPresentation->createSlide();
        generate_single_response_slide($currentSlide, $oPHPPresentation, $img_url, $scientist_name, $description);
    }
    # Now output something
}

?>
