<?php

namespace Database\Seeders;

use App\Models\MenuLink;
use App\Models\Page;
use A17\Twill\Models\Media;
use A17\Twill\Models\User;
use App\Repositories\MenuLinkRepository;
use App\Repositories\PageRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
{
    private const ASSETS_PATH = 'database/seeders/assets';

    private PageRepository $pageRepository;
    private MenuLinkRepository $menuRepository;
    private array $availableImages;
    private int $currentImageIndex = 0;

    public function __construct(
        PageRepository $pageRepository,
        MenuLinkRepository $menuRepository
    ) {
        $this->pageRepository = $pageRepository;
        $this->menuRepository = $menuRepository;
        $this->loadAvailableImages();
    }

    public function run(): void
    {
        $this->authenticateAdmin();
        $this->createPages();
    }

    private function authenticateAdmin(): void
    {
        Auth::guard('twill_users')->login(User::first());
    }

    private function loadAvailableImages(): void
    {
        $path = base_path(self::ASSETS_PATH);
        if (!File::isDirectory($path)) {
            throw new \RuntimeException(sprintf('Assets directory not found at: %s', $path));
        }

        $this->availableImages = collect(File::files($path))
            ->filter(function ($file) {
                return in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png']);
            })
            ->map(function ($file) {
                return $file->getPathname();
            })
            ->toArray();

        if (empty($this->availableImages)) {
            throw new \RuntimeException('No image files found in assets directory');
        }

        // Shuffle images to randomize initial assignment
        shuffle($this->availableImages);
    }

    private function getNextImage(): string
    {
        // If we've used all images, reset the index
        if ($this->currentImageIndex >= count($this->availableImages)) {
            $this->currentImageIndex = 0;
        }

        return $this->availableImages[$this->currentImageIndex++];
    }

    private function createMediaFromImage(string $imagePath, $page): Media
    {
        [$width, $height] = getimagesize($imagePath);
        $folderName = str_replace('-', '', Str::uuid());
        $filename = basename($imagePath);
        $uuid = $folderName . '/' . $filename;

        // Store the file
        Storage::disk(config('twill.media_library.disk'))
            ->putFileAs($folderName, $imagePath, $filename);

        return Media::create([
            'uuid' => $uuid,
            'filename' => $filename,
            'width' => $width,
            'height' => $height,
            'alt_text' => sprintf('%s cover image', $page->title),
            'caption' => sprintf('Cover image for %s page', $page->title)
        ]);
    }

    private function createPages(): void
    {
        foreach ($this->getPagesData() as $index => $pageData) {
            $page = $this->createPage($pageData, $index);
            $this->createMenuLink($page, $index);
        }
    }

    private function createPage(array $pageData, int $position): Page
    {
        $initialData = [
            'title' => ['en' => $pageData['title']],
            'description' => ['en' => $pageData['description']],
            'published' => true,
        ];

        $page = $this->pageRepository->create($initialData);

        $this->addPageContent($page, $pageData['content'], $position);
        $this->attachPageCoverImage($page);
        $this->createPageRevision($page, $initialData);

        return $page;
    }

    private function attachPageCoverImage(Page $page): void
    {
        $image = $this->getNextImage();
        $media = $this->createMediaFromImage($image, $page);

        $page->medias()->attach($media->id, [
            'role' => 'cover',
            'crop' => 'default',
            'metadatas' => json_encode([
                'default' => [
                    'crop_x' => 0,
                    'crop_y' => 0,
                    'crop_w' => $media->width,
                    'crop_h' => $media->height,
                ],
            ])
        ]);
    }

    private function addPageContent(Page $page, string $content, int $position): void
    {
        $blockData = [
            'editor_name' => 'default',
            'type' => 'text',
            'position' => $position,
            'content' => [
                'title' => ['en' => ''],
                'text' => ['en' => $content],
            ],
        ];

        $page->blocks()->create($blockData);
    }

    private function createPageRevision(Page $page, array $initialData): void
    {
        $this->pageRepository->createRevisionIfNeeded(
            $page,
            array_merge(
                $initialData,
                ['slug' => ['en' => $page->slug]],
                ['blocks' => [$page->blocks->first()->toArray()]]
            )
        );
    }

    private function createMenuLink(Page $page, int $position): void
    {
        $this->menuRepository->create([
            'published' => true,
            'title' => ['en' => $page->title],
            'position' => $position,
            'browsers' => [
                'page' => [[
                    'endpointType' => Page::class,
                    'id' => $page->id,
                ]]
            ]
        ]);
    }

    private function getPagesData(): array
    {
        return [
            [
                'title' => 'Home',
                'description' => 'Welcome to our company website',
                'content' => 'Welcome to our company! We are dedicated to providing exceptional services and innovative solutions to meet your needs. Explore our website to learn more about what we offer.',
            ],
            [
                'title' => 'About Us',
                'description' => 'Learn about our company history and values',
                'content' => 'Founded in 2020, our company has grown from a small startup to a recognized leader in our industry. We believe in innovation, quality, and customer satisfaction. Our team of experts is committed to delivering excellence in everything we do.',
            ],
            [
                'title' => 'Services',
                'description' => 'Explore our range of professional services',
                'content' => 'We offer a comprehensive range of services including consulting, development, and support. Our solutions are tailored to meet your specific requirements and help your business grow.',
            ],
            [
                'title' => 'Portfolio',
                'description' => 'View our recent projects and success stories',
                'content' => 'Browse through our portfolio of successful projects. Each case study demonstrates our expertise and commitment to delivering high-quality solutions that exceed client expectations.',
            ],
            [
                'title' => 'Contact',
                'description' => 'Get in touch with our team',
                'content' => 'We\'d love to hear from you! Contact us to discuss your project or learn more about our services. You can reach us by phone, email, or by filling out the contact form below.',
            ]
        ];
    }
}
