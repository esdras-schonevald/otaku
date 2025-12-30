# Modelagem de Domínios

Este documento orienta a evolução incremental do projeto Otaku, separando responsabilidades em domínios claros para preservar consistência, segurança e facilitar entregas por etapas.

## Domains
Domínios e subdomínios agrupam problemas que o negócio resolve. A implementação incremental segue a sequência abaixo:

```mermaid
    graph LR
    Partner --> Identity --> ServiceDelivery --> Provider --> Financial
    Invoicing --> Eventing --> Messaging --> Reporting --> Billing --> Infra

    style Billing           fill: #DDE, stroke: #EEF, color: #008
    style Eventing          fill: #DED, stroke: #EFE, color: #080
    style Financial         fill: #DEE, stroke: #EFF, color: #088
    style Identity          fill: #EDD, stroke: #FEE, color: #800
    style Infra             fill: #EDE, stroke: #FEF, color: #808
    style Invoicing         fill: #EED, stroke: #FFE, color: #880

    style ServiceDelivery   fill: #DDE, stroke: #EEF, color: #008
    style Messaging         fill: #DEE, stroke: #EFF, color: #088
    style Reporting         fill: #EDD, stroke: #FEE, color: #800
    style Partner           fill: #EDE, stroke: #FEF, color: #808
    style Provider          fill: #EED, stroke: #FFE, color: #880
```
