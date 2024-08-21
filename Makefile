build:
	docker compose build 
start: 
	docker compose up -d
stop:
	docker compose stop
clean:
	docker compose down
bash-php:
	docker exec -it php_image_edit bash
bash-mysql:
	docker exec -it mysql_container bash