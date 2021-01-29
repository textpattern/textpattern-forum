module.exports = function (grunt)
{
    'use strict';

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

        // Clean distribution directory to start afresh.
        clean: [
            'public/style/'
        ],

        // Run some tasks in parallel to speed up the build process.
        concurrent: {
            dist: [
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
                        src: '**',
                        dest: 'public/style/Textpattern/img/branding/'
                    },
                    {
                        expand: true,
                        cwd: 'node_modules/textpattern-branding/assets/img/apple-touch-icon/textpattern/',
                        src: '**',
                        dest: 'public/'
                    },
                    {
                        expand: true,
                        cwd: 'node_modules/textpattern-branding/assets/img/favicon/textpattern/',
                        src: '**',
                        dest: 'public/'
                    },
                    {
                        expand: true,
                        cwd: 'node_modules/textpattern-branding/assets/img/windows-site-tile/textpattern/',
                        src: '**',
                        dest: 'public/'
                    }
                ]
            },
            theme: {
                files: [
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
                        src: '**',
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
                    $: true,
                    jquery: true,
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
                implementation: require('sass'),
                outputStyle: 'expanded', // outputStyle = expanded, nested, compact or compressed.
                sourceMap: false
            },
            dist: {
                files: [
                    {'<%= paths.dest.css %>style.css': '<%= paths.src.sass %>style.scss'}
                ]
            }
        },

        // Validate CSS files via stylelint.
        stylelint: {
            options: {
                configFile: '.stylelintrc.yml'
            },
            src: ['<%= paths.src.sass %>**/*.{css,scss}']
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
        terser: {
            options: {
                ecma: 2015,
                compress: {
                    booleans_as_integers: true,
                    drop_console: true
                },
                format: {
                    comments: false
                }
            },
            dist: {
                files: [
                    {
                        '<%= paths.dest.js %>app.js': [
                            'node_modules/jquery/dist/jquery.slim.js',
                            'node_modules/prismjs/prism.js',
                            'node_modules/prismjs/components/prism-markup-templating.js',
                            'node_modules/prismjs/components/prism-apacheconf.js',
                            'node_modules/prismjs/components/prism-bash.js',
                            'node_modules/prismjs/components/prism-git.js',
                            'node_modules/prismjs/components/prism-json.js',
                            'node_modules/prismjs/components/prism-less.js',
                            'node_modules/prismjs/components/prism-markdown.js',
                            'node_modules/prismjs/components/prism-nginx.js',
                            'node_modules/prismjs/components/prism-perl.js',
                            'node_modules/prismjs/components/prism-php.js',
                            'node_modules/prismjs/components/prism-sass.js',
                            'node_modules/prismjs/components/prism-scss.js',
                            'node_modules/prismjs/components/prism-sql.js',
                            'node_modules/prismjs/components/prism-stylus.js',
                            'node_modules/prismjs/components/prism-textile.js',
                            '<%= paths.src.js %>app.js'
                        ]
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
    grunt.registerTask('build', ['clean', 'concurrent', 'terser']);
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('postsetup', ['shell:postsetup']);
    grunt.registerTask('css', ['stylelint', 'sass', 'postcss']);
    grunt.registerTask('setup', ['shell:setup', 'build']);
    grunt.registerTask('theme', ['copy:theme', 'replace']);
    grunt.registerTask('travis', ['build']);
};
