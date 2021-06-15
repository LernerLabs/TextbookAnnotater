<?php
header("Content-Description: File Transfer");
header("Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation");
header("Content-Disposition: attachment; filename=test.pptx"); // could fix this
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Expires: 0');
require_once 'src/PHPPresentation/src/PhpPresentation/Autoloader.php';
\PhpOffice\PhpPresentation\Autoloader::register();
require_once 'src/Common/src/Common/Autoloader.php';
\PhpOffice\Common\Autoloader::register();
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Shape\Drawing;

function generate_single_response_slide($currentSlide, $oPHPPresentation, $image_url, $scientist_name, $description){
    // API change: eventually, this gets called from a form, and so we don't bundle
    // everything up as a response. We call iwth the individual bits of data we need.
    # TODO: FIXME:
    # we used to do this when it was a file.
    #$shape = $currentSlide->createDrawingShape();
    #$image_filename = plugin_dir_path( __FILE__ ) . "Noether.jpg";
    #          ->setPath($image_filename)
    # The below code runs, but doesn't work. And, even if it did work, it would
    # only work if we had a JPEG. Need a more general version.

    //$image_url ="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e5/Noether.jpg/220px-Noether.jpg";
    // https://www.opentooleveryone.com/blog/phpoffice-phppresentation-creating-the-images-slide-dynamically-getting-the-source-from-some-db
    $imageData = "data:image/jpeg;base64,".base64_encode(file_get_contents($image_url));
    #list($width, $height) = getimagesize($image_url);
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

function generate_single_slide_presentation($image_url, $scientist_name, $description){
    # Now make the PPTX files
	$oPHPPresentation = new PhpPresentation();
	$currentSlide = $oPHPPresentation->getActiveSlide();
	generate_single_response_slide($currentSlide, $oPHPPresentation, $image_url, $scientist_name, $description);
    return $oPHPPresentation;
}


#$image_url = "https://upload.wikimedia.org/wikipedia/commons/thumb/e/e5/Noether.jpg/220px-Noether.jpg";
#$scientist_name = "A Scientist Name";
#$description = "A description of a Scientist";
$scientist_name = $_POST['scientist_name'];
$image_url = $_POST['image_url'];
$description = $_POST['description'];
$oPHPPresentation = generate_single_slide_presentation($image_url, $scientist_name, $description);
$oWriterPPTX = IOFactory::createWriter($oPHPPresentation, 'PowerPoint2007', $download=true);
$oWriterPPTX->save('php://output');
?>
