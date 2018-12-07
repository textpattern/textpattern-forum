module.exports = function (grunt)
{
    'use strict';

    // Define Sass implmentation (Dart-Sass).
    const Fiber = require('fibers');
    const sass = require('dart-sass');

    // Load all Grunt tasks.
    require('load-grunt-tasks')(grunt);

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        // Set up paths.
        paths: {
            src: {
                sass: 'src/style/Textpattern/sass/',
                js: 'src/style/Textpattern/js/',
                templates: 'src/style/Textpattern/'
            },
            dest: {
                css: 'public/style/Textpattern/css/',
                js: 'public/style/Textpattern/js/',
                templates: 'public/style/Textpattern/'
            }
        },

        // Set up timestamp.
        opt : {
            timestamp: '<%= new Date().getTime() %>'
        },

        // Bundle up the JavaScript.
        browserify: {
            development: {
                src: [
                    '<%= paths.src.js %>app.js'
                ],
                dest: '<%= paths.dest.js %>app.js',
                options: {
                    browserifyOptions: {
                        debug: false
                    },
                    transform: [[
                        'babelify', {
                            'presets': ['@babel/preset-env']
                        }
                    ]]
                }
            }
        },

        // Clean distribution directory to start afresh.
        clean: [
            'public/style/'
        ],

        // Run some tasks in parallel to speed up the build process.
        concurrent: {
            dist: [
                'browserify',
                'copy:branding',
                'css',
                'jshint',
                'theme'
            ]
        },

        // Copy files from `src/` and `node_modules/` to `public/`.
        copy: {
            branding: {
                files: [
                    {
                        expand: true,
                        cwd: 'node_modules/textpattern-branding/assets/img/',
                        src: ['**'],
                        dest: 'public/style/Textpattern/img/branding/'
                    },
                    {
                        expand: true,
                        cwd: 'node_modules/textpattern-branding/assets/img/apple-touch-icon/textpattern/',
                        src: ['**'],
                        dest: 'public/'
                    },
                    {
                        expand: true,
                        cwd: 'node_modules/textpattern-branding/assets/img/favicon/textpattern/',
                        src: ['**'],
                        dest: 'public/'
                    },
                    {
                        expand: true,
                        cwd: 'node_modules/textpattern-branding/assets/img/windows-site-tile/textpattern/',
                        src: ['**'],
                        dest: 'public/'
                    }
                ]
            },
            theme: {
                files: [
                    {
                        expand: true,
                        cwd: 'src/style/imports/',
                        src: ['**'],
                        dest: 'public/style/imports/'
                    },
                    {
                        expand: true,
                        cwd: 'src/style/',
                        src: ['Textpattern.css', '.htaccess'],
                        dest: 'public/style/'
                    },
                    {
                        expand: true,
                        cwd: '<%= paths.src.templates %>',
                        src: ['**', '!*.tpl', '!sass/**', '!js/**'],
                        dest: '<%= paths.dest.templates %>'
                    },
                    {
                        expand: true,
                        cwd: 'src/lib/',
                        src: ['**'],
                        dest: 'public/lib/'
                    },
                ]
            }
        },

        // Check code quality of Gruntfile.js and site-specific JavaScript using JSHint.
        jshint: {
            options: {
                bitwise: true,
                browser: true,
                curly: true,
                eqeqeq: true,
                esversion: 6,
                forin: true,
                globals: {
                    module: true,
                    require: true,
                    Prism: true
                },
                latedef: true,
                noarg: true,
                nonew: true,
                strict: true,
                undef: true,
                unused: false
            },
            files: [
                'Gruntfile.js',
                'src/style/Textpattern/js/app.js'
            ]
        },

        // Add vendor prefixed styles and other post-processing transformations.
        postcss: {
            options: {
                processors: [
                    require('autoprefixer'),
                    require('cssnano')
                ]
            },
            dist: {
                src: '<%= paths.dest.css %>*.css'
            }
        },

        // Generate latest timestamp within theme template files.
        replace: {
            theme: {
                options: {
                    patterns: [
                        {
                            match: 'timestamp',
                            replacement: '<%= opt.timestamp %>'
                        },
                        {
                            match: 'year',
                            replacement: '<%= new Date().getFullYear() %>'
                        }
                    ]
                },
                files: [
                    {
                        expand: true,
                        cwd: '<%= paths.src.templates %>',
                        src: ['*.tpl'],
                        dest: '<%= paths.dest.templates %>'
                    },
                    {
                        expand: true,
                        cwd: 'src/',
                        src: ['*.html'],
                        dest: 'public/'
                    }
                ]
            }
        },

        // Sass configuration.
        sass: {
            options: {
                fiber: Fiber,
                implementation: sass,
                outputStyle: 'expanded', // outputStyle = expanded, nested, compact or compressed.
                sourceMap: false
            },
            dist: {
                files: [
                    {'<%= paths.dest.css %>style.css': '<%= paths.src.sass %>style.scss'}
                ]
            }
        },

        // Validate Sass files via sass-lint.
        sasslint: {
            options: {
                configFile: '.sass-lint.yml'
            },
            target: ['<%= paths.src.sass %>**/*.scss']
        },

        // Run forum setup and postsetup scripts.
        shell: {
            setup: {
                command: [
                    'php src/setup/setup.php'
                ].join('&&'),
                options: {
                    stdout: true
                }
            },
            postsetup: {
                command: [
                    'php src/setup/post.php'
                ].join('&&'),
                options: {
                    stdout: true
                }
            }
        },

        // Minify `app.js`.
        uglify: {
            dist: {
                files: [
                    {
                        '<%= paths.dest.js %>app.js': ['<%= paths.dest.js %>app.js']
                    }
                ]
            }
        },

        // Directories watched and tasks performed by invoking `grunt watch`.
        watch: {
            sass: {
                files: '<%= paths.src.sass %>**/*.scss',
                tasks: 'css'
            },
            js: {
                files: '<%= paths.src.js %>**',
                tasks: [
                    'jshint',
                    'browserify',
                    'uglify'
                ]
            },
            theme: {
                files: [
                    '!src/style/*/sass/**',
                    '!src/style/*/js/**',
                    'src/style/*/**',
                    'src/*.html'
                ],
                tasks: 'theme'
            }
        }
    });

    // Register tasks.
    grunt.registerTask('build', ['clean', 'concurrent', 'uglify']);
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('postsetup', ['shell:postsetup']);
    grunt.registerTask('css', ['sasslint', 'sass', 'postcss']);
    grunt.registerTask('setup', ['shell:setup', 'build']);
    grunt.registerTask('theme', ['copy:theme', 'replace']);
    grunt.registerTask('travis', ['build']);
};
