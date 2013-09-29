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

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        watch: {
            sass: {
                files: ['src/style/*.scss', 'src/style/*/sass/**'],
                tasks: ['sass']
            },

			theme: {
				files: ['!src/style/*/sass/**', '!src/style/*/js/**', 'src/style/*/**'],
				tasks: ['copy:theme']
			},

            js: {
                files: 'src/style/*/js/**',
                tasks: ['jshint', 'uglify', 'copy:js']
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
			js: {
				files: [
					{expand: true, cwd: 'src/style/Textpattern/js/libs/', src: ['**'], dest: 'public/style/Textpattern/js/'}
				]
			},

			theme: {
				files: [
					{expand: true, cwd: 'src/style/Textpattern/', src: ['**', '!sass/**', '!js/**'], dest: 'public/style/Textpattern/'}
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
                    'public/style/Textpattern.css': ['tmp/style/Textpattern.css'],
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
                        'public/style/Textpattern/js/main.js': ['src/style/Textpattern/js/main.js']
                    }
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
            }
        }
    });

    grunt.registerTask('test', ['jshint']);
    grunt.registerTask('sass', ['compass', 'cssmin']);
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('build', ['jshint', 'copy', 'sass', 'uglify']);
    grunt.registerTask('travis', ['jshint']);
    grunt.registerTask('setup', ['shell:setup']);
};
