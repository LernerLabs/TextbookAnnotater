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

# maybe make a function that takes a student response ID, gives you a file to download.
# write two functions. One takes a single student response ID and gets it from the DB and makes the PPTX
# another takes a textbook ID.

function generate_single_response_slide($currentSlide, $response){
	 $image_filename = plugin_dir_path( __FILE__ ) . "./vendor/phpoffice/phppresentation/samples/resources/phppowerpoint_logo.gif";
	 $image_filename = plugin_dir_path( __FILE__ ) . "Noether.jpg";
	 $shape = $currentSlide->createDrawingShape();
	 # We'll assume about 96 pixels per inch.
	 $shape->setName('PHPPresentation logo')
	       ->setDescription('PHPPresentation logo')
	       ->setPath($image_filename)
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
	$textRun = $shape->createTextRun($response->student_name); # should be scientist name
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
	$oRichText->createTextRun('Description of Scientist');
	$oRichText->createParagraph()->createTextRun('Flork bork');
	$oRichText->createParagraph()->createTextRun($response->description);
}

function generate_single_slide_presentation($response){
        # Now make the PPTX files
	$oPHPPresentation = new PhpPresentation();
	$currentSlide = $oPHPPresentation->getActiveSlide();
	generate_single_response_slide($currentSlide, $response);
	return $oPHPPresentation;
#	# output would look like this
#	       	 $outfname = preg_replace("/[^A-Za-z0-9]/", '', $response->student_name); # should be scientist name
#        	 $outfname = $outfname . ".pptx";
#
#		 
#        
#        	 $oWriterPPTX = IOFactory::createWriter($oPHPPresentation, 'PowerPoint2007');
#        	 $oWriterPPTX->save(__DIR__ . "/" . $outfname );
}

function generate_textbook_slide_deck($textbook_id){
	 $all_student_responses = get_all_student_responses();
	 $oPHPPresentation = new PhpPresentation();
	 foreach($all_student_responses as $response){
	         $currentSlide = $oPHPPresentation->createSlide();
		 generate_single_response_slide($currentSlide, $response);
	 }
	 # Now output something
}

?>
