<?php declare(strict_types = 1);

namespace WebChemistry\Images\Image;

use Nette\InvalidArgumentException;
use WebChemistry\Images\Resources\IResource;

class Image extends \Nette\Utils\Image implements IImage {

    const EXTENSIONS = [
        'jpeg' => Image::JPEG,
        'jpg' => Image::JPEG,
        'png' => Image::PNG,
        'gif' => Image::GIF,
        'webp' => Image::WEBP,
    ];

    /** @var int|null */
	private $quality;

    /** @var int|null */
    private $type;

	/**
	 * @param int|null $quality
	 * @return static
	 */
	public function setQuality(?int $quality): self {
		$this->quality = $quality;

		return $this;
	}

    /**
     * @param int|null $type
     * @return static
     */
    public function setType(?int $type): self {
        $this->type = $type;

        return $this;
    }

	/**
	 * Saves image to the file.
	 * @param string $file  filename
	 * @param int|null $quality  quality (0..100 for JPEG and WEBP, 0..9 for PNG)
	 * @param int $type  optional image type
	 */
	public function save(string $file, int $quality = null, int $type = null): void {
        parent::save($this->changeFileExtension($file), $quality === null ? $this->quality : $quality, $type === null ? $this->type : $type);
    }

	/**
	 * @param string $file
	 *
	 * @return string
	 */
	private function changeFileExtension(string $file): string {
	    if (!$this->type)
	        return $file;
	    $newExtension = Image::typeToExtension($this->type);
	    $oldExtension = Image::typeToExtension(Image::detectTypeFromFile($file));
		return str_replace($oldExtension, $newExtension, $file);
	}

	/**
	 * @param IResource|string $resource
	 *
	 * @return int
	 * @throws \Nette\InvalidArgumentException
	 */
	public static function getImageType($resource): int {
		$resource = $resource instanceof IResource ? $resource->getName() : $resource;
		$extensions = self::EXTENSIONS;
		if (!isset($extensions[$extension = strtolower(pathinfo($resource, PATHINFO_EXTENSION))])) {
			throw new InvalidArgumentException("Unsupported file extension '$extension'.");
		}

		return $extensions[$extension];
	}

}
