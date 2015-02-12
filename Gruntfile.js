module.exports = function (grunt)
{
    'use strict';

    // Load Grunt plugins.
    grunt.loadNpmTasks('grunt-combine-media-queries');
    grunt.loadNpmTasks('grunt-contrib-compass');
    grunt.loadNpmTasks('grunt-contrib-compress');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-replace');
    grunt.loadNpmTasks('grunt-shell');

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        // Set up timestamp.
        opt : {
            timestamp: '<%= new Date().getTime() %>'
        },

        // Use 'config.rb' file to configure Compass.
        compass: {
            dev: {
                options: {
                    config: 'config.rb',
                    force: true
                }
            }
        },

        // Combine any matching media queries.
        cmq: {
            css: {
                files: {
                    'tmp/style/Textpattern/sass': [
                        'tmp/style/Textpattern/sass/*.css'
                    ]
                }
            }
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
                    {expand: true, cwd: 'bower_components/textpattern-branding/assets/img/', src: ['**'], dest: 'public/style/Textpattern/img/branding/'},
                    {expand: true, cwd: 'bower_components/textpattern-branding/assets/img/apple-touch-icon/textpattern/', src: ['**'], dest: 'public/'},
                    {expand: true, cwd: 'bower_components/textpattern-branding/assets/img/favicon/textpattern/', src: ['**'], dest: 'public/'},
                    {expand: true, cwd: 'bower_components/textpattern-branding/assets/img/windows-site-tile/textpattern/', src: ['**'], dest: 'public/'}
                ]
            },

            theme: {
                files: [
                    {expand: true, cwd: 'src/style/imports/', src: ['**'], dest: 'public/style/imports/'},
                    {expand: true, cwd: 'src/style/', src: ['Textpattern.css', '.htaccess'], dest: 'public/style/'},
                    {expand: true, cwd: 'src/style/Textpattern/', src: ['**', '!*.tpl', '!sass/**', '!js/**'], dest: 'public/style/Textpattern/'}
                ]
            }
        },

        // Concatenate, minify and copy CSS files to `public/`.
        cssmin: {
            main: {
                options: {
                    rebase: false
                },
                files: {
                    'public/style/Textpattern/css/main.css': ['tmp/style/Textpattern/sass/main.css'],
                    'public/style/Textpattern/css/ie8.css': ['tmp/style/Textpattern/sass/ie8.css']
                }
            }
        },

        // Check code quality of Gruntfile.js and site-specific JavaScript using JSHint.
        jshint: {
            files: ['Gruntfile.js', 'src/style/*/js/*.js'],
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
                    responsiveNav: true,
                    prettyPrint: true
                }
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
                    {expand: true, cwd: 'src/style/Textpattern/', src: ['*.tpl'], dest: 'public/style/Textpattern/'},
                    {expand: true, cwd: 'src/', src: ['*.html'], dest: 'public/'}
                ]
            }
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
                        'public/style/Textpattern/js/main.js': ['src/style/Textpattern/js/main.js'],
                        'public/style/Textpattern/js/prettify.js': ['bower_components/google-code-prettify/src/prettify.js'],
                        'public/style/Textpattern/js/require.js': ['bower_components/requirejs/require.js'],
                        'public/style/Textpattern/js/cookie.js': ['bower_components/jquery.cookie/jquery.cookie.js'],
                        'public/style/Textpattern/js/responsivenav.js': ['bower_components/responsive-nav/responsive-nav.js'],
                        'public/style/Textpattern/js/autosize.js': ['bower_components/jquery-autosize/jquery.autosize.js']
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
                files: ['src/style/*.scss', 'src/style/*/sass/**'],
                tasks: ['sass', 'compress:theme']
            },

            theme: {
                files: ['!src/style/*/sass/**', '!src/style/*/js/**', 'src/style/*/**', 'src/*.html'],
                tasks: ['theme']
            },

            js: {
                files: 'src/style/*/js/**',
                tasks: ['jshint', 'uglify', 'compress:theme']
            }
        }
    });

    // Register tasks.
    grunt.registerTask('build', ['jshint', 'theme', 'copy:branding', 'sass', 'uglify', 'compress:theme']);
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('postsetup', ['shell:postsetup']);
    grunt.registerTask('sass', ['compass', 'cmq', 'cssmin']);
    grunt.registerTask('setup', ['shell:setup', 'build']);
    grunt.registerTask('test', ['jshint']);
    grunt.registerTask('theme', ['copy:theme', 'replace:theme']);
    grunt.registerTask('travis', ['jshint', 'compass']);
};
