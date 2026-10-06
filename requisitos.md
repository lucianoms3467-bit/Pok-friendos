# Calculadora de tipagens

## Objetivos
o sistema vai ver as tipagens dos Pokémon e calcular suas fraquezas, resitências, imundades e super efetivos. Com um Pokémon de um tipo ou dois tipos diferentes somando as características de cada tipo, assim, conseguindo as características do Pokémon cujo nome foi pesquisado na barra de pesquisa.

### Stack Tecnológico
 Backend: PHP estruturado com sessões nativas
 banco de dados: MySQL (PDO para segurança)
 Frontend: HTMLS, PHP, CSS, Bootstrap CSS

#### Regras Globais
-Use sempre PDO para conexão e queries no MySQL para evitar SQL injections
-Mantenha o código limpo e comente apenas logicas complexas
-Separe os arquivos de lógica: um arquivo para conexão com base (bd.php) e scripts de backend isolados e view em HTMLS/PHP
-Estilize as telas em Bootstrap de forma responsiva priorizando o MobileFrist
-Retorne sempre mensagens de erros de formas claras na interface para usuário (TOAST)
-Sempre trate as mensagens de caixa de mensagens nativas do navegador em MODAL
