module.exports = function (grunt)
{
    'use strict';

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-compass');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-shell');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-replace');
    grunt.loadNpmTasks('grunt-contrib-compress');

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
                files: ['!src/style/*/sass/**', '!src/style/*/js/**', 'src/style/*/**'],
                tasks: ['theme']
            },

            js: {
                files: 'src/style/*/js/**',
                tasks: ['jshint', 'uglify', 'copy:js', 'compress:theme']
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

            js: {
                files: [
                    {expand: true, cwd: 'src/style/Textpattern/js/libs/', src: ['**'], dest: 'public/style/Textpattern/js/'}
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
                    prettyPrint: true,
                    WebFont: true
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
                    mangle: false,
                    preserveComments: 'some'
                },

                files: [
                    {
                        'public/style/Textpattern/js/main.js': ['src/style/Textpattern/js/main.js'],
                        'public/style/Textpattern/js/placeholder.js': ['bower_components/jquery-placeholder/jquery.placeholder.js'],
                        'public/style/Textpattern/js/prettify.js': ['bower_components/google-code-prettify/src/prettify.js'],
                        'public/style/Textpattern/js/require.js': ['bower_components/requirejs/require.js'],
                        'public/style/Textpattern/js/responsivenav.js': ['bower_components/responsive-nav/responsive-nav.js']
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
    grunt.registerTask('build', ['jshint', 'copy:js', 'theme', 'copy:branding', 'sass', 'uglify', 'compress:theme']);
    grunt.registerTask('theme', ['copy:theme', 'replace:theme']);
    grunt.registerTask('travis', ['jshint']);
    grunt.registerTask('setup', ['shell:setup', 'build']);
    grunt.registerTask('postsetup', ['shell:postsetup']);
};
