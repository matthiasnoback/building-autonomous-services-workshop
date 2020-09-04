FROM nginx:stable-alpine
COPY template.conf /etc/nginx/conf.d/site.template
COPY nginx.conf /etc/nginx/nginx.conf
CMD sh -c "envsubst '\$SERVER_NAME \$ROOT \$PHP_BACKEND' < /etc/nginx/conf.d/site.template > /etc/nginx/conf.d/default.conf && nginx -g 'daemon off;'"
