<?php
  /**
   * Drush Recipes API
   *
   * This documents ways you can modify the behavior of drush recipes as well
   * as how to create your own recipes.
   */

// You can define additional dr_locations to search for in your .drush/drushrc.php
// settings file so that you can stash recipes in a shared location like box.com
// to use this add something like:
// $options['dr_locations'] = '/drushstuff';
// or
// $options['dr_locations'] = array('/drushstuff', '/drecipes');

// HOOKS

/**
 * Implements hook_drush_recipes_pre_cook_alter().
 * A list of recipes to cook prior to their actual execution. This would allow
 * you to act upon what has been requested and automatically add other recipes
 * or modify based on your own needs.
 * @param  array $list    a list of recipes to cook
 * @param  bool $recurse  if we are recursing
 */
function hook_drush_recipes_pre_cook_alter(&$list, $recurse) {

}

/**
 * Implements hook_drush_recipes_command_invoke_alter().
 * This hook fires just prior to any command being executed in a drush recipe
 * ingredient list. This fires per command listed and allows the developer to
 * react to the command about to be fired for spidering, listening, and utterly
 * ridiculous branching logic.
 * @param  mixed  $command  a command array / string based on format
 * @param  string $format   DRUSH_RECIPES_FORMAT_ARGUMENT or DRUSH_RECIPES_FORMAT_TARGET
 */
function hook_drush_recipes_command_invoke_alter(&$command, $format) {
  // if you see an sql sync about to execute, we have other things to do
  if ($command['command'] == 'sql-sync') {
    // tap our github hooks / do a new git commit of whatever is laying around
    // so that we can trip travis and get feedback about what's happening here
  }
}

/**
 * Implements hook_drush_recipes_post_cook_alter().
 * This hook fires just after recipes have finished cooking. Since this function
 * could be fired in a recursive manner this may invoke multiple times so be
 * aware of when you are jumping into the operation.
 * @param  array $list    a list of recipes to cook
 * @param  bool $recurse  if we are recursing
 */
function hook_drush_recipes_post_cook_alter(&$list, $recurse) {

}

/**
 * Implements hook_drush_recipes_locations_alter().
 * Add custom locations for where to spider and look for drecipes.
 * @param  array $locations an array of directories to search for recipes
 */
function hook_drush_recipes_locations_alter(&$locations) {

}

/**
 * Implements hook_drush_recipes_system_recipe_data_alter().
 * This allows you to modify the recipes that are loaded in from files just
 * after they have been loaded in. You can use this to modify recipes or inject
 * your own recipes that you want to add on the fly without the need for
 * .drecipe files.
 * @param  object $recipes loaded file objects referencing recipe arrays
 */
function hook_drush_recipes_system_recipe_data_alter(&$recipes) {

}

/**
 * Implements hook_drush_recipes_after_recipe_loaded_alter().
 * This allows for modification of a recipe just after it has been loaded from
 * a flat file.
 *
 * @param  array $recipe a fully loaded array from a file without file object
 */
function hook_drush_recipes_after_recipe_loaded_alter(&$recipe) {

}

/**
 * Implements hook_drush_recipes_to_drush_alter().
 * This allows for modification of a recipe just before it is converted into
 * drush statements. You could use this to jump in and inject ingredients based
 * on what you see in the current recipe or remove commands that you don't like
 * firing (like a complex security routine where you like all but 1 setting).
 *
 * @param  array $recipe a recipe structure being converted to drush statements
 */
function hook_drush_recipes_to_drush_alter(&$recipe) {

}

/**
 * Implements hook_drush_recipes_encode_alter().
 * This allows for modifying the output just before it is typically written to
 * a file or printed to the screen.
 *
 * @param  string $contents  output of a recipe for export
 * @param  string $format    format of the export data; json, xml, or yaml
 */
function hook_drush_recipes_encode_alter(&$contents, $format) {

}


/**
 * Drush Recipe 1.0
 *
 * name - Human reable name of this recipe; required
 * drush_recipes_api - api version, 1.0 defaults
 * core - drupal core this is compatible with, optional
 * weight - weight relative to other recipes if called in the same block-chain
 *   this defaults to 0
 * dependencies - drush plugin name, drush command, module names, or @site which
 *   means that the command requires a working drupal site to function.
 *   All four are valid dependency types and can be used together but try to
 *   use @site, plugin name or module name when possible, drush command is lazy
 *   and doesn't give good feedback to other developers as to how to meet the
 *   requirement.
 * conflicts - a list of recipes that are known conflicts. Use this if you are
 *   building multiple recipes and you know that they don't play nice together.
 *   This can help save you or others from accidentally trying to run recipes
 *   that do and undo each others functionality (or a security recipe known
 *   to have issues with something that opens up doors).
 * recipe - the structure of commands to execute, this can also be another
 *   recipe filename which will append all the commands in that file ahead of
 *   what is about to execute. There are 4 structures to this listed below
 * metadata - a series of properties that can be used for front-end integration.
 *   this is entirely optional but helps developers understand what you wrote.
 */
$js = <<<JS
{
  "name": "Security defaults",
  "drush_recipes_api": "1.0",
  "weight": 0,
  "core": "7",
  "dependencies": [
    "seckit",
    "paranoia"
  ],
  "conflicts": [
    "insecure_stuff"
  ],
  "recipe": [
    "dr_admin_update_status.drecipe",
    [
      "vset",
      "user_register",
      "1"
    ],
    [
      "dis",
      "php"
    ],
    [
      "pm-uninstall",
      "php"
    ],
    [
      "en",
      "seckit"
    ],
    [
      "en",
      "paranoia"
    ]
  ],
  "metadata": {
    "descrption": "Disable projects that cause issues, increase h-our defenses cap'in!",
    "version": "1.0",
    "type": "add-on",
    "author": "btopro",
    "logo": "files\/image.png"
  }
}
JS>>>;

$js = <<<JS
// here's the 4 major types of calls you can do in a recipe as of the 1.0 spec.
"recipe": [
    "dr_admin_update_status.drecipe", // reference another recipe
    [
      "vset", // each element is an argument passed into a 1 line call w/ drush as prefix
      "user_register",
      "1"
    ],
    [
      "conditional": [ // allow for user to select which sub-routine to run
        "recipe1.drecipe",
        "recipe2.drecipe",
        "recipe3.drecipe"
      ]
    ],
    [// long call format, this allows user to interact w/ the call
    // only use this for complex call structures that you need to interact with
    "target": "cool.d7.site", //target
    "arguments": [ // arguments to pass in, similar to simple structure
      "en",
      "paranoia"
    ],
    "options": ["y"] // any possible options
    ]
  ],
JS>>>;
