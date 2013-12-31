module.exports = function (grunt)
{
    'use strict';

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

        opt : {
            timestamp: '<%= new Date().getTime() %>'
        },

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
        },

        compass: {
            dev: {
                options: {
                    config: 'config.rb',
                    force: true
                }
            }
        },

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

        cssmin: {
            main: {
                files: {
                    'public/style/Textpattern/css/main.css': ['tmp/style/Textpattern/sass/main.css'],
                    'public/style/Textpattern/css/ie8.css': ['tmp/style/Textpattern/sass/ie8.css']
                }
            }
        },

        uglify: {
            dist: {
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
                        'public/style/Textpattern/js/autosize.js': ['bower_components/jquery-autosize/jquery.autosize.js'],
                        'public/style/Textpattern/js/html5shiv.js': ['bower_components/html5shiv/dist/html5shiv.js']
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
        }
    });

    grunt.registerTask('test', ['jshint']);
    grunt.registerTask('sass', ['compass', 'cssmin']);
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('build', ['jshint', 'theme', 'copy:branding', 'sass', 'uglify', 'compress:theme']);
    grunt.registerTask('theme', ['copy:theme', 'replace:theme']);
    grunt.registerTask('travis', ['jshint', 'compass']);
    grunt.registerTask('setup', ['shell:setup', 'build']);
    grunt.registerTask('postsetup', ['shell:postsetup']);
};
