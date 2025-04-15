FROM docker.elastic.co/elasticsearch/elasticsearch:7.10.1

ENV discovery.type=single-node

COPY elasticsearch.yml /usr/share/elasticsearch/config/elasticsearch.yml

EXPOSE 9200 9300

USER elasticsearch