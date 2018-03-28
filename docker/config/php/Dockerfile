FROM centos:7

RUN yum install -y http://rpms.famillecollet.com/enterprise/remi-release-7.rpm

RUN yum update -y \
    && yum install -y --enablerepo=remi-php70 php php-fpm php-gd php-mbstring php-intl php-mcrypt php-pdo php-xml php-mysql php-zip php-pecl-memcache php-pecl-redis

RUN yum clean all

COPY php.ini /etc/php.d/50-setting.ini
COPY www.conf /etc/php-fpm.d/www.conf

EXPOSE 9000

WORKDIR /var/www/html

ENTRYPOINT ["/usr/sbin/php-fpm", "-F"]