FROM docker.elastic.co/kibana/kibana:7.10.1

COPY ./config/kibana.yml /usr/share/kibana/config/kibana.yml

ENV ELASTICSEARCH_HOSTS=http://elasticsearch:9200

EXPOSE 5601

CMD ["kibana"]