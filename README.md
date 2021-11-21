## Getting Started

Clone the repository:
git clone https://github.com/veretilosergei1985/blog.git

Run docker containers:
docker-compose up -d --build

Set up DB and fixtures:
docker-compose exec symfony-test-core bash
php bin/console doctrine:schema:update --force
php bin/console doctrine:fixtures:load
