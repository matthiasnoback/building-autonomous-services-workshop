FROM nginx:1.13-alpine
COPY template.conf /etc/nginx/conf.d/site.template
CMD sh -c "envsubst '\$SERVER_NAME \$ROOT \$PHP_BACKEND' < /etc/nginx/conf.d/site.template > /etc/nginx/conf.d/default.conf && nginx -g 'daemon off;'"