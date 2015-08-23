#!/usr/bin/env php
<?php

// We get the project name from the name of the path that Composer created for us.
$projectname = basename(realpath("."));
echo "projectname $projectname taken from directory name\n";

// We could do more replaces to our templates here,
// for the example we only do {{ projectname }}
$replaces = [
    "{{ projectname }}" => $projectname,
    "{{ projectid }}" => $projectname . 'dev'
];


// Process templates from skel/templates dir. Notice that we only use files that end
// with -dist again. This makes sense in the context of this example, but depending on your
// requirements you might want to do a more complex things here (like if you want
// to replace files somewhere
// else than in the projects root directory
foreach (glob("skel/templates/{,.}*-dist", GLOB_BRACE) as $distfile) {

    $target = substr($distfile, 15, -5);

    $target = realpath(".") . '/' . $target;
    $distfile = realpath(".") . '/' . $distfile;

    // First we copy the dist file to its new location,
    // overwriting files we might already have there.
    echo "creating clean file ($target) from dist ($distfile)...\n";
    copy($distfile, $target);

    // Then we apply our replaces for within those templates.
    echo "applying variables to $target...\n";
    applyValues($target, $replaces);
}
echo "removing dist files\n";

// Then we drop the skel dir, as it contains skeleton stuff.
delTree("skel");

// We could also remove the composer.phar that the zend skeleton has here,
// but a much better choice is to remove that one from our fork directly.

echo "\033[0;32mdist script done...\n";


/**
 * A method that will read a file, run a strtr to replace placeholders with
 * values from our replace array and write it back to the file.
 *
 * @param string $target the filename of the target
 * @param array $replaces the replaces to be applied to this target
 */
function applyValues($target, $replaces)
{
    file_put_contents(
        $target,
        strtr(
            file_get_contents($target),
            $replaces
        )
    );
}


/**
 * A simple recursive delTree method
 *
 * @param string $dir
 * @return bool
 */
function delTree($dir)
{
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}

exit(0);