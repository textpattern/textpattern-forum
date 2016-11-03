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
                js: 'src/style/Textpattern/js/'
            },
            tmp: {
                css: 'tmp/assets/css/',
                js: 'tmp/assets/js/'
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

        // Clean distribution and temporary directories to start afresh.
        clean: [
            'tmp/',
            '<%= paths.dest.templates %>*/'
        ],

        // Run some tasks in parallel to speed up the build process.
        concurrent: {
            dist: [
                'css',
                'copy:branding',
                'devUpdate',
                'jshint',
                'replace'
            ]
        },

        // Gzip compress the theme files.
        compress: {
            theme: {
                options: {
                    mode: 'gzip'
                },
                files: [
                    {expand: true, src: ['public/style/**/*.js'], ext: '.js.gz'},
                    {expand: true, src: ['public/style/**/*.css'], ext: '.css.gz'},
                    {expand: true, src: ['public/style/**/*.svg'], ext: '.svg.gz'}
                ]
            }
        },

        // Copy files from `src/` and `bower_components/` to `public/`.
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
                        cwd: 'src/style/Textpattern/',
                        src: ['**', '!*.tpl', '!sass/**', '!js/**'],
                        dest: 'public/style/Textpattern/'
                    }
                ]
            }
        },

        // Report on any available updates for dependencies.
        devUpdate: {
            main: {
                options: {
                    updateType: 'report',
                    reportUpdated: false, // Don't report up-to-date packages.
                    packages: {
                        dependencies: true,
                        devDependencies: true
                    }
                }
            }
        },

        // Check code quality of Gruntfile.js and site-specific JavaScript using JSHint.
        jshint: {
            options: {
                bitwise: true,
                camelcase: true,
                curly: true,
                eqeqeq: true,
                es3: true,
                forin: true,
                immed: true,
                indent: 4,
                latedef: true,
                noarg: true,
                noempty: true,
                nonew: true,
                quotmark: 'single',
                undef: true,
                unused: true,
                strict: true,
                trailing: true,
                browser: true,
                globals: {
                    jQuery: true,
                    Zepto: true,
                    define: true,
                    module: true,
                    require: true,
                    requirejs: true,
                    autosize: true,
                    responsiveNav: true,
                    prettyPrint: true
                }
            },
            files: [
                'Gruntfile.js',
                'src/style/*/js/*.js'
            ]
        },

        // Add vendor prefixed styles and other post-processing transformations.
        postcss: {
            options: {
                processors: [
                    require('autoprefixer')({
                        browsers: [
                            'last 3 versions',
                            'not ie <= 11'
                        ]
                    }),
                    require('cssnano')()
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
                        cwd: 'src/style/Textpattern/',
                        src: ['*.tpl'],
                        dest: 'public/style/Textpattern/'
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
                outputStyle: 'expanded', // outputStyle = expanded, nested, compact or compressed.
                sourceMap: false
            },
            dist: {
                files: [
                    {'<%= paths.dest.css %>style.css': '<%= paths.src.sass %>style.scss'},
                    {'<%= paths.dest.css %>design-patterns.css': '<%= paths.src.sass %>design-patterns.scss'}
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

        // Uglify and copy JavaScript files from `bower-components`, and also `main.js`, to `public/assets/js/`.
        uglify: {
            dist: {
                // Preserve all comments that start with a bang (!) or include a closure compiler style.
                options: {
                    preserveComments: 'some'
                },

                files: [
                    {
                        '<%= paths.dest.js %>main.js': ['<%= paths.src.js %>main.js'],
                        '<%= paths.dest.js %>autosize.js': ['node_modules/autosize/dist/autosize.js'],
                        '<%= paths.dest.js %>cookie.js': ['node_modules/jquery.cookie/jquery.cookie.js'],
                        '<%= paths.dest.js %>prettify.js': ['bower_components/google-code-prettify/src/prettify.js'],
                        '<%= paths.dest.js %>require.js': ['node_modules/requirejs/require.js'],
                        '<%= paths.dest.js %>responsivenav.js': ['node_modules/responsive-nav/responsive-nav.js']
                    },
                    {
                        expand: true,
                        cwd: 'bower_components/google-code-prettify/src/',
                        src: 'lang-*.js',
                        dest: 'public/style/Textpattern/js/'
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
                tasks: ['jshint', 'uglify', 'compress:theme']
            },
            theme: {
                files: ['!src/style/*/sass/**', '!src/style/*/js/**', 'src/style/*/**', 'src/*.html'],
                tasks: ['theme']
            }
        }
    });

    // Register tasks.
    grunt.registerTask('build', ['clean', 'theme', 'concurrent', 'uglify', 'compress:theme']);
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('postsetup', ['shell:postsetup']);
    grunt.registerTask('css', ['sasslint', 'sass', 'postcss']);
    grunt.registerTask('setup', ['shell:setup', 'build']);
    grunt.registerTask('theme', ['copy:theme', 'replace:theme']);
    grunt.registerTask('travis', ['jshint', 'compass']);
};
