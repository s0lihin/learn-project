FROM docker.elastic.co/logstash/logstash:7.10.1

COPY logstash.conf /usr/share/logstash/pipeline/logstash.conf

USER logstash

CMD ["logstash", "-f", "/usr/share/logstash/pipeline/logstash.conf"]