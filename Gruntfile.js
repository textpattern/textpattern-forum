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
                curly: true,
                eqeqeq: true,
                esversion: 5,
                forin: true,
                globals: {
                    jQuery: true,
                    Zepto: true,
                    define: true,
                    module: true,
                    require: true,
                    requirejs: true,
                    Prism: true
                },
                latedef: true,
                noarg: true,
                nonew: true,
                quotmark: 'single',
                undef: true,
                unused: false,
                strict: true,
                browser: true
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
                    },
                    {
                        src: '<%= paths.src.js %>main.js',
                        dest: '<%= paths.dest.js %>main.js'
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

        // Uglify and copy JavaScript files from `bower-components`, and also `main.js`, to `public/assets/js/`.
        uglify: {
            dist: {
                // Preserve all comments that start with a bang (!) or include a closure compiler style.
                options: {
                    output: {
                        comments: require('uglify-save-license')
                    }
                },
                files: [
                    {
                        '<%= paths.dest.js %>main.js': ['<%= paths.dest.js %>main.js'],
                        '<%= paths.dest.js %>prism.js': [
                            'node_modules/prismjs/prism.js',
                            // Add any plugins
                            // Add any additional languages
                            'node_modules/prismjs/components/prism-markup-templating.js',
                            'node_modules/prismjs/components/prism-apacheconf.js',
                            'node_modules/prismjs/components/prism-bash.js',
                            'node_modules/prismjs/components/prism-coffeescript.js',
                            'node_modules/prismjs/components/prism-git.js',
                            'node_modules/prismjs/components/prism-haml.js',
                            'node_modules/prismjs/components/prism-json.js',
                            'node_modules/prismjs/components/prism-less.js',
                            'node_modules/prismjs/components/prism-markdown.js',
                            'node_modules/prismjs/components/prism-nginx.js',
                            'node_modules/prismjs/components/prism-perl.js',
                            'node_modules/prismjs/components/prism-php.js',
                            'node_modules/prismjs/components/prism-ruby.js',
                            'node_modules/prismjs/components/prism-sass.js',
                            'node_modules/prismjs/components/prism-scss.js',
                            'node_modules/prismjs/components/prism-sql.js',
                            'node_modules/prismjs/components/prism-stylus.js',
                            'node_modules/prismjs/components/prism-textile.js',
                            'node_modules/prismjs/components/prism-txp.js',
                            'node_modules/prismjs/components/prism-yaml.js'
                        ],
                        '<%= paths.dest.js %>require.js': ['node_modules/requirejs/require.js']
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
    grunt.registerTask('build', ['clean', 'concurrent', 'uglify']);
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('postsetup', ['shell:postsetup']);
    grunt.registerTask('css', ['sasslint', 'sass', 'postcss']);
    grunt.registerTask('setup', ['shell:setup', 'build']);
    grunt.registerTask('theme', ['copy:theme', 'replace']);
    grunt.registerTask('travis', ['jshint', 'build']);
};
