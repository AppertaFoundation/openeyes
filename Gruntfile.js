module.exports = function(grunt) {
        grunt.initConfig({
 
        compass: {
                OpenEyes: {
                        options: {              
                                sassDir: '../openeyes/protected/assets/sass',
                                cssDir: '../openeyes/protected/assets/css',
                                imagesDir: '../openeyes/protected/assets/img',
                                generatedImagesDir: '../openeyes/protected/assets/img/sprites',
                                outputStyle: 'expanded',
                                relativeAssets: true,
                                httpPath: '',
                                noLineComments: false,
                                importPath: [
                                        '../openeyes/protected/assets/components/foundation/scss/',
                                        '/var/www/openeyes/protected/assets/components/foundation/scss/'
                                ]
                        }
                },

                OphCiExamination: {
                        options: {
                                sassDir: '../openeyes/protected/modules/OphCiExamination/assets/sass',
                                cssDir: '../openeyes/protected/modules/OphCiExamination/assets/css',
                                generatedImagesDir: '../openeyes/protected/modules/OphCiExamination/assets/img/sprites',
                                outputStyle: 'expanded',
                                relativeAssets: true,
                                httpPath: '',
                                noLineComments: false,
                                importPath: [
                                        '../openeyes/protected/assets/components/foundation/scss/',
                                        '/var/www/openeyes/protected/assets/components/foundation/scss/',
                                        '../openeyes/protected/assets/sass/',
                                        '/var/www/openeyes/protected/assets/sass/'
                                ]
                        }
                },

                OphCiPhasing: {
                        options: {
                                sassDir: '../openeyes/protected/modules/OphCiPhasing/assets/sass',
                                cssDir: '../openeyes/protected/modules/OphCiPhasing/assets/css',
                                imagesDir: '../openeyes/protected/modules/OphCiPhasing/assets/img',
                                generatedImagesDir: '../openeyes/protected/assets/img/sprites',
                                outputStyle: 'expanded',
                                relativeAssets: true,
                                httpPath: '',
                                noLineComments: false,
                                importPath: [
                                           '../openeyes/protected/assets/components/foundation/scss/',
                                                '/var/www/openeyes/protected/assets/components/foundation/scss/',
                                                '../openeyes/protected/assets/sass/',
                                                '/var/www/openeyes/protected/assets/sass/'
                                ]
                        }
                },

                OphCoCorrespondence: {
                        options: {
                                sassDir: '../openeyes/protected/modules/OphCoCorrespondence/assets/sass',
                                cssDir: '../openeyes/protected/modules/OphCoCorrespondence/assets/css',
                                imagesDir: '../openeyes/protected/modules/OphCoCorrespondence/assets/img',
                                outputStyle: 'expanded',
                                relativeAssets: true,
                                httpPath: '',
                                noLineComments: false,
                                importPath: [
                                        '../openeyes/protected/assets/components/foundation/scss/',
                                         '/var/www/openeyes/protected/assets/components/foundation/scss/',
                                         '../openeyes/protected/assets/sass/',
                                         '/var/www/openeyes/protected/assets/sass/'
                                ]
                        }
                },

                OphCoTherapyapplication: {
                        options: {
                                sassDir: '../openeyes/protected/modules/OphCoTherapyapplication/assets/sass',
                                cssDir: '../openeyes/protected/modules/OphCoTherapyapplication/assets/css',
                                imagesDir: '../openeyes/protected/modules/OphCoTherapyapplication/assets/img',
                                generatedImagesDir: '../openeyes/protected/assets/img/sprites',
                                outputStyle: 'expanded',
                                relativeAssets: true,
                                httpPath: '',
                                noLineComments: false,
                                importPath: [
                                        '../openeyes/protected/assets/components/foundation/scss/',
                                         '/var/www/openeyes/protected/assets/components/foundation/scss/',
                                         '../openeyes/protected/assets/sass/',
                                         '/var/www/openeyes/protected/assets/sass/'
                                ]
                        }
                },

                OphDrPrescription: {
                        options: {
                                sassDir: '../openeyes/protected/modules/OphDrPrescription/assets/sass',
                                cssDir: '../openeyes/protected/modules/OphDrPrescription/assets/css',
                                imagesDir: '../openeyes/protected/modules/OphDrPrescription/assets/img',
                                generatedImagesDir: '../openeyes/protected/modules/OphDrPrescription/assets/img/sprites',
                                outputStyle: 'expanded',
                                relativeAssets: true,
                                httpPath: '',
                                noLineComments: false,
                                importPath: [
                                        '../openeyes/protected/assets/components/foundation/scss/',
                                         '/var/www/openeyes/protected/assets/components/foundation/scss/',
                                         '../openeyes/protected/assets/sass/',
                                         '/var/www/openeyes/protected/assets/sass/'
                                ]
                        }
                },

                OphInBiometry: {
                        options: {
                                sassDir: '../openeyes/protected/modules/OphInBiometry/assets/sass',
                                cssDir: '../openeyes/protected/modules/OphInBiometry/assets/css',
                                imagesDir: '../openeyes/protected/modules/OphInBiometry/assets/img',
                                generatedImagesDir: '../openeyes/protected/modules/OphInBiometry/assets/img/sprites',
                                outputStyle: 'expanded',
                                relativeAssets: true,
                                httpPath: '',
                                noLineComments: false,
                                importPath: [
                                        '../openeyes/protected/assets/components/foundation/scss/',
                                         '/var/www/openeyes/protected/assets/components/foundation/scss/',
                                         '../openeyes/protected/assets/sass/',
                                         '/var/www/openeyes/protected/assets/sass/'
                                ]
                        }
                },

                OphInVisualfields: {
                        options: {
                                sassDir: '../openeyes/protected/modules/OphInVisualfields/assets/sass',
                                cssDir: '../openeyes/protected/modules/OphInVisualfields/assets/css',
                                imagesDir: '../openeyes/protected/modules/OphInVisualfields/assets/img',
                                generatedImagesDir: '../openeyes/protected/modules/OphInVisualfields/assets/img/sprites',
                                outputStyle: 'expanded',
                                relativeAssets: true,
                                httpPath: '',
                                noLineComments: false,
                                importPath: [
                                        '../openeyes/protected/assets/components/foundation/scss/',
                                        '/var/www/openeyes/protected/assets/components/foundation/scss/',
                                        '../openeyes/protected/assets/sass/',
                                        '/var/www/openeyes/protected/assets/sass/'
                                ]
                        }
                },

                OphOuAnaestheticsatisfactionaudit: {
                        options: {
                                sassDir: '../openeyes/protected/modules/OphOuAnaestheticsatisfactionaudit/assets/sass',
                                cssDir: '../openeyes/protected/modules/OphOuAnaestheticsatisfactionaudit/assets/css',
                                imagesDir: '../openeyes/protected/modules/OphOuAnaestheticsatisfactionaudit/assets/img',
                                generatedImagesDir: '../openeyes/protected/modules/OphOuAnaestheticsatisfactionaudit/assets/img/sprites',
                                outputStyle: 'expanded',
                                relativeAssets: true,
                                httpPath: '',
                                noLineComments: false,
                                importPath: [
                                        '../openeyes/protected/assets/components/foundation/scss/',
                                        '/var/www/openeyes/protected/assets/components/foundation/scss/',
                                        '../openeyes/protected/assets/sass/',
                                        '/var/www/openeyes/protected/assets/sass/'
                                ]
                        }
                },

                OphTrConsent: {
                        options: {
                                sassDir: '../openeyes/protected/modules/OphTrConsent/assets/sass',
                                cssDir: '../openeyes/protected/modules/OphTrConsent/assets/css',
                                imagesDir: '../openeyes/protected/modules/OphTrConsent/assets/img',
                                generatedImagesDir: '../openeyes/protected/modules/OphTrConsent/assets/img/sprites',
                                outputStyle: 'expanded',
                                relativeAssets: true,
                                httpPath: '',
                                noLineComments: false,
                                importPath: [
                                        '../openeyes/protected/assets/components/foundation/scss/',
                                        '/var/www/openeyes/protected/assets/components/foundation/scss/',
                                        '../openeyes/protected/assets/sass/',
                                        '/var/www/openeyes/protected/assets/sass/'
                                ]
                        }
                },

                OphTrIntravitrealinjection: {
                        options: {
                                sassDir: '../openeyes/protected/modules/OphTrIntravitrealinjection/assets/sass',
                                cssDir: '../openeyes/protected/modules/OphTrIntravitrealinjection/assets/css',
                                imagesDir: '../openeyes/protected/modules/OphTrIntravitrealinjection/assets/img',
                                generatedImagesDir: '../openeyes/protected/modules/OphTrIntravitrealinjection/assets/img/sprites',
                                outputStyle: 'expanded',
                                relativeAssets: true,
                                httpPath: '',
                                noLineComments: false,
                                importPath: [
                                        '../openeyes/protected/assets/components/foundation/scss/',
                                        '/var/www/openeyes/protected/assets/components/foundation/scss/',
                                        '../openeyes/protected/assets/sass/',
                                        '/var/www/openeyes/protected/assets/sass/'
                                ]
                        }
                },

                OphTrLaser: {
                        options: {
                                sassDir: '../openeyes/protected/modules/OphTrLaser/assets/sass',
                                cssDir: '../openeyes/protected/modules/OphTrLaser/assets/css',
                                imagesDir: '../openeyes/protected/modules/OphTrLaser/assets/img',
                                generatedImagesDir: '../openeyes/protected/modules/OphTrLaser/assets/img/sprites',
                                outputStyle: 'expanded',
                                relativeAssets: true,
                                httpPath: '',
                                noLineComments: false,
                                importPath: [
                                        '../openeyes/protected/assets/components/foundation/scss/',
                                        '/var/www/openeyes/protected/assets/components/foundation/scss/',
                                        '../openeyes/protected/assets/sass/',
                                        '/var/www/openeyes/protected/assets/sass/'
                                ]
                        }
                },

                OphTrOperationbooking: {
                        options: {
                                sassDir: '../openeyes/protected/modules/OphTrOperationbooking/assets/sass',
                                cssDir: '../openeyes/protected/modules/OphTrOperationbooking/assets/css',
                                imagesDir: '../openeyes/protected/modules/OphTrOperationbooking/assets/img',
                                generatedImagesDir: '../openeyes/protected/modules/OphTrOperationbooking/assets/img/sprites',
                                outputStyle: 'expanded',
                                relativeAssets: true,
                                httpPath: '',
                                noLineComments: false,
                                importPath: [
                                        '../openeyes/protected/assets/components/foundation/scss/',
                                        '/var/www/openeyes/protected/assets/components/foundation/scss/',
                                        '../openeyes/protected/assets/sass/',
                                        '/var/www/openeyes/protected/assets/sass/'
                                ]
                        }
                },

                OphTrOperationnote: {
                        options: {
                                sassDir: '../openeyes/protected/modules/OphTrOperationnote/assets/sass',
                                cssDir: '../openeyes/protected/modules/OphTrOperationnote/assets/css',
                                imagesDir: '../openeyes/protected/modules/OphTrOperationnote/assets/img',
                                generatedImagesDir: '../openeyes/protected/modules/OphTrOperationnote/assets/img/sprites',
                                outputStyle: 'expanded',
                                relativeAssets: true,
                                httpPath: '',
                                noLineComments: false,
                                importPath: [
                                        '../openeyes/protected/assets/components/foundation/scss/',
                                        '/var/www/openeyes/protected/assets/components/foundation/scss/',
                                        '../openeyes/protected/assets/sass/',
                                        '/var/www/openeyes/protected/assets/sass/'
                                ]
                        }
                },

                PatientTicketing: {
                        options: {
                                sassDir: '../openeyes/protected/modules/PatientTicketing/assets/sass',
                                cssDir: '../openeyes/protected/modules/PatientTicketing/assets/css',
                                imagesDir: '../openeyes/protected/modules/PatientTicketing/assets/img',
                                generatedImagesDir: '../openeyes/protected/modules/PatientTicketing/assets/img/sprites',
                                outputStyle: 'expanded',
                                relativeAssets: true,
                                httpPath: '',
                                noLineComments: false,
                                importPath: [
                                        '../openeyes/protected/assets/components/foundation/scss/',
                                        '/var/www/openeyes/protected/assets/components/foundation/scss/',
                                        '../openeyes/protected/assets/sass/',
                                        '/var/www/openeyes/protected/assets/sass/'
                                ]
                        }
                }
        },
   

        uglify: {
                eyedraw1: {
                        files: {
                                '../openeyes/protected/modules/eyedraw/assets/js/dist/eyedraw.min.js': ['../openeyes/protected/modules/eyedraw/assets/js/dist/eyedraw.js']
                        }
                },
                eyedraw2: {
                        files: {
                                '../openeyes/protected/modules/eyedraw/assets/js/dist/oe-eyedraw.min.js': ['../openeyes/protected/modules/eyedraw/assets/js/dist/oe-eyedraw.js']
                        }
                }
        }


        });

        grunt.loadNpmTasks('grunt-contrib-compass');
        grunt.loadNpmTasks('grunt-contrib-uglify');

        grunt.registerTask('default', ['compass', 'uglify']);
};

