WP Theme Image
=========================

Class for autogenerating HTML for WordPress theme image.

Works just like HTMLImage class, but autogenerates the FileLoader to fit WordPress’s theme directory.

Local directory within theme directory can be set on an image-by-image basis by setting the “directory” key in the $attributes hash map to the desired local directory string.

For example, in a theme named “blue” setup on localhost:

    use WaughJ\WPThemeImage\WPThemeImage;

    $image = new WPThemeImage( 'demo.png', [ 'directory' => 'img' ] );
    $image->print(); // Will print <img src="http://localhost.com/wp-content/themes/blue/img/demo.png?m=4389109" alt=""> ( #s after m= will vary ).

Or you can globally set the default local directory with the static method setDefaultSharedDirectory():

    use WaughJ\WPThemeImage\WPThemeImage;

    $image = new WPThemeImage( 'demo.png' );
    $image->print(); // Will print <img src="http://localhost.com/wp-content/themes/blue/demo.png?m=4389109" alt=""> ( #s after m= will vary ).

    WPThemeImage::setDefaultSharedDirectory( 'pictures' );

    $image = new WPThemeImage( 'demo.png' );
    $image->print(); // Will now print <img src="http://localhost.com/wp-content/themes/blue/pictures/demo.png?m=4389109" alt=""> ( #s after m= will vary ).

## Error Handling

As a child o’ HTMLImage, WPThemeImage will throw an exception if it’s set to show versioning ( default ) & can’t find the file on the server. Read the HTMLImage documentation to learn mo’ ’bout this. WPThemeImage works the same way:

    try
    {
    	$image = new WPThemeImage( 'missing.jpg' );
    }
    catch ( MissingFileException $e )
    {
    	$image = $e->getFallbackContent();
    }
    $image->print() // Will print <img src="http://localhost.com/wp-content/themes/blue/missing.jpg" alt=""> without throwing an error.
