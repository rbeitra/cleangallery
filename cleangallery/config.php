<?php

//Google Analytics tracking id. set to '' if not needed
define('GA_ID', '');


//Should all the galleries be publicly listed?
define('LIST_GALLERIES', TRUE);


//Should we go straight to a gallery folder rather than displaying a list? set to '' to ignore
//This will effectively also make LIST_GALLERIES be FALSE
define('SINGLE_GALLERY', '');


//What size should the thumbnails be?
define('THUMB_WIDTH', 256);
define('THUMB_HEIGHT', 256);

//What should be the in the title bar and at the top of pages?
define('SITE_NAME', 'Photos');


//Where are the galleries?
define('GALLERIES_DIR', './galleries/');

//Where should we cache the thumbnails?
define('THUMBS_DIR', './thumbs/');


//Print the warnings and things
define('NOTIFICATIONS', TRUE);


//Print the page generation time
define('GENERATE_TIME', FALSE);



?>