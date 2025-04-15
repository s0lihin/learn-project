FROM grafana/grafana:latest

COPY config/grafana.ini /etc/grafana/grafana.ini

USER grafana

EXPOSE 3000

CMD ["/run.sh"]