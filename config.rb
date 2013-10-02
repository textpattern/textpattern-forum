# Singularity GS - https://github.com/Team-Sass/Singularity
require "singularitygs"

http_path = "./"
project_path = "src"
sass_path = "src/style"
css_path = "tmp/style"
images_dir = "img"
fonts_dir = "fonts"
output_style = :expanded
environment = :production
Sass::Script::Number.precision = 7
