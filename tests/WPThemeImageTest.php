
<?php
use PHPUnit\Framework\TestCase;
use WaughJ\FileLoader\MissingFileException;
use WaughJ\WPThemeImage\WPThemeImage;
use WaughJ\HTMLImage\MalformedSrcSetStringException;

require_once( 'MockWordPress.php' );

class WPThemeImageTest extends TestCase
{
	public function testBasicImage()
	{
		$image = new WPThemeImage( 'img/favicon.png' );
		$this->assertEquals( $image->getHTML(), '<img src="https://www.example.com/wp-content/themes/example/tests/img/favicon.png?m=' . filemtime( getcwd() . '/tests/img/favicon.png'  ) . '" alt="" />' );
	}

	public function testWithShareDirectory()
	{
		$image = new WPThemeImage( 'photo.jpg', [ 'directory' => 'img' ] );
		$this->assertEquals( $image->getHTML(), '<img src="https://www.example.com/wp-content/themes/example/tests/img/photo.jpg?m=' . filemtime( getcwd() . '/tests/img/photo.jpg'  ) . '" alt="" />' );
	}

	public function testNonExistentFile()
	{
		try
		{
			$image = new WPThemeImage( 'jibber.jpg', [ 'directory' => 'img' ] );
		}
		catch ( MissingFileException $e )
		{
			$image = $e->getFallbackContent();
		}
		$this->assertEquals( $image->getHTML(), '<img src="https://www.example.com/wp-content/themes/example/tests/img/jibber.jpg" alt="" />' );
	}

	public function testNoCache()
	{
		$image = new WPThemeImage( 'photo.jpg', [ 'directory' => 'img', 'show-version' => false ] );
		$this->assertEquals( $image->getHTML(), '<img src="https://www.example.com/wp-content/themes/example/tests/img/photo.jpg" alt="" />' );

	}
	public function testWithExtraAttributes()
	{
		$image = new WPThemeImage( 'photo.jpg', [ 'directory' => 'img', 'id' => 'portrait-img', 'class' => 'center-img portrait', 'width' => '320', 'height' => 320, 'alt' => 'Windmill Trails', 'srcset' => 'photo.jpg 320w, photo-2x.jpg 640w' ] );
		$this->assertStringContainsString( ' src="https://www.example.com/wp-content/themes/example/tests/img/photo.jpg?m=' . filemtime( getcwd() . '/tests/img/photo.jpg'  ) . '"', $image->getHTML() );
		$this->assertStringContainsString( ' width="320"', $image->getHTML() );
		$this->assertStringContainsString( ' height="320"', $image->getHTML() );
		$this->assertStringContainsString( ' id="portrait-img"', $image->getHTML() );
		$this->assertStringContainsString( ' class="center-img portrait"', $image->getHTML() );
		$this->assertStringContainsString( ' alt="Windmill Trails"', $image->getHTML() );
		$this->assertStringContainsString( ' srcset="https://www.example.com/wp-content/themes/example/tests/img/photo.jpg?m=' . filemtime( getcwd() . '/tests/img/photo.jpg'  ) . ' 320w, https://www.example.com/wp-content/themes/example/tests/img/photo-2x.jpg?m=' . filemtime( getcwd() . '/tests/img/photo-2x.jpg'  ) . ' 640w"', $image->getHTML() );
	}

	public function testMalformedSrcString()
	{
		$this->expectException( MalformedSrcSetStringException::class );
		$image = new WPThemeImage( 'photo.jpg', [ 'directory' => 'img', 'id' => 'portrait-img', 'class' => 'center-img portrait', 'width' => '320', 'height' => 320, 'alt' => 'Windmill Trails', 'srcset' => 'photo.jpg 320w, photo-2x.jpg' ] );
	}

	public function testPartialVersioning()
	{
		try
		{
			$image = new WPThemeImage( 'photo.jpg', [ 'directory' => 'img', 'srcset' => 'photo-320x240.jpg 320w, photo-800x400.jpg 800w, photo-2560x900.jpg 2560w' ] );
		}
		catch ( MissingFileException $e )
		{
			$image = $e->getFallbackContent();
		}
		$this->assertStringContainsString( ' src="https://www.example.com/wp-content/themes/example/tests/img/photo.jpg?m=', $image->getHTML() );
		$this->assertStringContainsString( ' srcset="https://www.example.com/wp-content/themes/example/tests/img/photo-320x240.jpg?m=', $image->getHTML() );
		$this->assertStringContainsString( 'https://www.example.com/wp-content/themes/example/tests/img/photo-2560x900.jpg 2560w"', $image->getHTML() );

		try
		{
			$image = new WPThemeImage( 'missing.jpg', [ 'directory' => 'img', 'srcset' => 'photo-320x240.jpg 320w, photo-800x400.jpg 800w, photo-2560x900.jpg 2560w' ] );
		}
		catch ( MissingFileException $e )
		{
			$image = $e->getFallbackContent();
		}
		$this->assertStringContainsString( ' src="https://www.example.com/wp-content/themes/example/tests/img/missing.jpg"', $image->getHTML() );
		$this->assertStringContainsString( ' srcset="https://www.example.com/wp-content/themes/example/tests/img/photo-320x240.jpg?m=', $image->getHTML() );
		$this->assertStringContainsString( 'https://www.example.com/wp-content/themes/example/tests/img/photo-2560x900.jpg 2560w"', $image->getHTML() );
	}

	public function testSetDefault()
	{
		WPThemeImage::setDefaultSharedDirectory( 'img' );
		$image = new WPThemeImage( 'photo.jpg' );
		$this->assertEquals( '<img src="https://www.example.com/wp-content/themes/example/tests/img/photo.jpg?m=' . filemtime( getcwd() . '/tests/img/photo.jpg' ) . '" alt="" />', $image->getHTML() );
	}
}
