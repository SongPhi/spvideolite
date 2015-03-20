module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    copy: {
      main: {
        nonull: true,
        files: [
          {expand: true, cwd: 'bower_components/videojs/dist/video-js/', src: ['*.js'], dest: 'static/js/videojs/'},
          {expand: true, cwd: 'bower_components/videojs/dist/video-js/lang', src: ['*'], dest: 'static/js/videojs/lang/'},
          {expand: true, cwd: 'bower_components/videojs/dist/video-js/', src: ['*.css'], dest: 'static/css/videojs/'},
          {expand: true, cwd: 'bower_components/videojs/dist/video-js/', src: ['font/*'], dest: 'static/css/videojs/'},
        ]
      }          
    }
  });

  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-clean');
  
  // Default task(s).
  grunt.registerTask('default', ['copy']);

};