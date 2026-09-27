<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BladeViewsTest extends TestCase
{
    /**
     * Test that all Blade view templates compile without PHP syntax errors.
     */
    public function test_all_blade_views_compile_without_syntax_errors(): void
    {
        $viewsPath = resource_path('views');
        $files = File::allFiles($viewsPath);

        $errors = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());

                // Check for duplicate closing tags or common Blade typos
                if (substr_count($content, '</x-layouts.app>') > 1) {
                    $errors[] = $file->getRelativePathname() . ' : Duplicate </x-layouts.app> found.';
                }
                if (substr_count($content, '</x-ui.button>') > substr_count($content, '<x-ui.button')) {
                    $errors[] = $file->getRelativePathname() . ' : Duplicate </x-ui.button> tag found.';
                }
            }
        }

        $this->assertEmpty($errors, "Errors found in Blade views:\n" . implode("\n", $errors));
    }
}
