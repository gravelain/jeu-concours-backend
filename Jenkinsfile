pipeline {
    agent any

    environment {
        //VPS_CREDENTIALS_ID = '6R2hV:H:C#T2stpcKK:g'
        //VPS_HOST = '92.113.27.65'
        VPS_USER = 'root'
        APP_REMOTE_PATH = '/srv/app/preprod'
        DOCKER_COMPOSE_REMOTE_PATH = '/srv/app/preprod/backend/.docker'
        GIT_REPO_URL = 'https://gitlab.com/furious-ducks1/backend.git'
        GIT_BRANCH = 'preprod'
        APP_URL = 'http://92.113.27.65:81'
        COMPOSE_FILE = '.docker/docker-compose.yml'
        BACKEND_SERVICE = 'php'
        DB_SERVICE = 'mysql'
        MYSQL_ROOT_PASSWORD = 'hello'
        MYSQL_DATABASE = 'app_db'
        MYSQL_USER = 'app_user'
        MYSQL_PASSWORD = 'hello'
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Cleanup Old Containers') {
            steps {
                script {
                    sh "docker-compose -f ${env.COMPOSE_FILE} down --volumes --remove-orphans || true"
                }
            }
        }

        stage('Build and Start Services Locally') {
            steps {
                script {
                    withEnv([
                        "MYSQL_ROOT_PASSWORD=${env.MYSQL_ROOT_PASSWORD}",
                        "MYSQL_DATABASE=${env.MYSQL_DATABASE}",
                        "MYSQL_USER=${env.MYSQL_USER}",
                        "MYSQL_PASSWORD=${env.MYSQL_PASSWORD}"
                    ]) {
                        sh "docker-compose -f ${env.COMPOSE_FILE} up -d --build"
                    }
                }
            }
        }

        stage('Verify File Permissions') {
            steps {
                script {
                    sh "docker-compose -f ${env.COMPOSE_FILE} exec -T ${env.BACKEND_SERVICE} chmod +x bin/console || true"
                }
            }
        }

        

        stage('Run Database Migrations') {
            steps {
                script {
                    withEnv([
                        "MYSQL_ROOT_PASSWORD=${env.MYSQL_ROOT_PASSWORD}",
                        "MYSQL_DATABASE=${env.MYSQL_DATABASE}",
                        "MYSQL_USER=${env.MYSQL_USER}",
                        "MYSQL_PASSWORD=${env.MYSQL_PASSWORD}"
                    ]) {
                        sh """
                        docker-compose -f ${env.COMPOSE_FILE} exec -T ${env.BACKEND_SERVICE} bin/console doctrine:database:create --if-not-exists
                        docker-compose -f ${env.COMPOSE_FILE} exec -T ${env.BACKEND_SERVICE} bin/console doctrine:schema:update --force
                        """
                    }
                }
            }
        }

//         stage('Run Tests') {
//             steps {
//                 script {
//                     sh """
//                     # Créer la base de données de test si elle n'existe pas
//                     docker-compose -f ${env.COMPOSE_FILE} exec -T db bash -c '
//                         mysql -u root -phello -e "CREATE DATABASE IF NOT EXISTS app_db_test;"
//                     '
//
//                     # Exécuter les migrations ou les schémas si nécessaire
//                     docker-compose -f ${env.COMPOSE_FILE} exec -T php bash -c '
//                         vendor/bin/console doctrine:migrations:migrate --env=test --no-interaction
//                     '
//                     """
//
//                     sh """
//                     docker-compose -f ${env.COMPOSE_FILE} exec -T php bash -c '
//                         export APP_ENV=test &&
//                         vendor/bin/phpunit
//                     '
//                     """
//                 }
//             }
//         }

        stage('Deploy to VPS') {
            steps {
                script {
                    sshagent(credentials: [env.VPS_CREDENTIALS_ID]) {
                        sh """
                        ssh -o StrictHostKeyChecking=no ${env.VPS_USER}@${env.VPS_HOST} << EOF
                            cd ${env.APP_REMOTE_PATH}
                            git fetch origin
                            git checkout ${env.GIT_BRANCH}
                            git pull origin ${env.GIT_BRANCH}

                            cd ${env.DOCKER_COMPOSE_REMOTE_PATH}
                            docker-compose -f docker-compose.preprod.yml down --volumes --remove-orphans
                            docker-compose -f docker-compose.preprod.yml up -d --build

                            docker-compose -f docker-compose.preprod.yml exec -T ${env.BACKEND_SERVICE} composer install

                            docker-compose -f docker-compose.preprod.yml exec -T ${env.BACKEND_SERVICE} php bin/console doctrine:migrations:migrate --no-interaction

                            docker system prune -f
                        EOF
                        """
                    }
                }
            }
        }

        stage('Verify Deployment') {
            steps {
                script {
                    sshagent(credentials: [env.VPS_CREDENTIALS_ID]) {
                        sh """
                        ssh -o StrictHostKeyChecking=no ${env.VPS_USER}@${env.VPS_HOST} << EOF
                            docker-compose -f ${env.DOCKER_COMPOSE_REMOTE_PATH}/docker-compose.yml ps

                            curl -f ${env.APP_URL} || (echo "L'application ne répond pas!" && exit 1)
                        EOF
                        """
                    }
                }
            }
        }
    }

    post {
        always {
            script {
                sh "docker-compose -f ${env.COMPOSE_FILE} down || true"
                cleanWs()
            }
        }
        success {
            echo "Déploiement de la préprod réussi !"
        }
        failure {
            echo "Échec du déploiement de la préprod"
        }
    }
}
