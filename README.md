# ⚡ Pok-friendos - Calculadora de Tipagens Pokémon

Aplicação web desenvolvida estritamente de acordo com as especificações do arquivo [`requisitos.md`](requisitos.md) do repositório [Pok-friendos](https://github.com/lucianoms3467-bit/Pok-friendos.git).

---

## 🎯 Objetivos do Projeto
O sistema analisa as tipagens dos Pokémon e calcula automaticamente:
- **Super Fraquezas (4x):** Quando ambos os tipos do Pokémon sofrem fraqueza (2 × 2 = 4x).
- **Fraquezas (2x):** Dano dobrado contra a combinação de tipos.
- **Resistências (½x ou 0.5x):** Dano reduzido pela metade.
- **Super Resistências (¼x ou 0.25x):** Quando ambos os tipos resistem (0.5 × 0.5 = 0.25x).
- **Imunidades (0x):** Dano totalmente anulado por imunidade natural.
- **Super Efetivos no Ataque (STAB - 2x):** Tipagens contra as quais os ataques do mesmo tipo deste Pokémon causam dano super efetivo.
- **Dano Neutro (1x):** Tipagens normais sem modificadores de dano.

Além disso, possui:
- **Barra de pesquisa** com sugestões em tempo real para pesquisar qualquer Pokémon por nome ou ID da Pokédex (ex: *Pikachu*, *Charizard*, *Gengar*, *Lucario*, *25*).
- **Seletor manual de tipos** para simular combinações personalizadas de um ou dois tipos.
- Exibição de atributos base (*HP, Ataque, Defesa, Sp. Atk, Sp. Def, Velocidade*), peso, altura e artwork oficial.

---

## 🛠️ Stack Tecnológico & Regras Globais

| Camada | Tecnologia Utilizada | Conformidade com os Requisitos |
|---|---|---|
| **Backend** | **PHP Estruturado** com sessões nativas | `session_start()`, histórico de pesquisas em `$_SESSION` |
| **Banco de Dados** | **MySQL via PDO** | Prepared Statements (`$pdo->prepare()`) para blindagem contra **SQL Injection** |
| **Frontend** | **HTML5, CSS3, Bootstrap 5** | Design responsivo priorizando **Mobile-First** |
| **Alertas & Erros** | **Bootstrap Toasts** | Mensagens claras de status e validações em toasts na interface |
| **Caixas de Diálogo** | **Bootstrap Modals** | Substituição completa de `alert()` e `confirm()` nativos do navegador por modais |

---

## 📁 Estrutura de Arquivos

```text
c:\Users\Aluno\Desktop\calculadora\
├── bd.php                      # Conexão PDO segura com MySQL (Prepared Statements)
├── database.sql                # Script SQL de criação do banco pokefriendos e dados iniciais
├── index.php                   # View principal em PHP + HTML5 com Bootstrap 5
├── index.html                  # Versão cliente autônoma para visualização rápida no navegador
├── requisitos.md               # Requisitos originais do projeto
├── README.md                   # Documentação detalhada da aplicação
├── backend/
│   ├── tipos_dados.php         # Matriz completa de efetividades dos 18 tipos Pokémon
│   ├── calcular.php            # Script isolado de backend para cálculo de dano (PDO + Fallback)
│   ├── buscar.php              # Script isolado de busca (MySQL PDO + PokeAPI + Cache)
│   ├── setup.php               # Script de auto-instalação do banco e tabelas via PDO
│   └── historico.php           # Gerenciamento de histórico de buscas em sessões PHP
├── views/
│   ├── header.php              # Cabeçalho HTML5, Navbar Pokédex e CDNs Bootstrap
│   ├── footer.php              # Rodapé e inclusão de scripts
│   ├── modal.php               # Modais Bootstrap reutilizáveis (sem alert nativo)
│   └── toast.php               # Contêiner de notificações Toast do Bootstrap
└── assets/
    ├── css/
    │   └── style.css           # Estilos personalizados, badges oficiais e tema Pokédex
    └── js/
        └── app.js              # Lógica frontend, autocompletes, modais e cálculos
```

---

## 🚀 Como Executar o Projeto

### Opção 1: Visualização Imediata (Sem Servidor Web)
Abra diretamente o arquivo [`index.html`](index.html) com dois cliques no Google Chrome ou Microsoft Edge. A calculadora executará todos os cálculos, buscas com PokeAPI, modais e toasts instantaneamente no navegador.

### Opção 2: Com Servidor PHP Embutido
1. Abra o terminal (PowerShell ou CMD) na pasta do projeto:
   ```powershell
   cd c:\Users\Aluno\Desktop\calculadora
   php -S localhost:8000
   ```
2. Acesse no navegador: `http://localhost:8000`

### Opção 3: Com XAMPP / Laragon / WAMP (PHP + MySQL)
1. Copie a pasta `calculadora` para a pasta raiz do seu servidor (ex: `C:\xampp\htdocs\calculadora`).
2. Abra o **phpMyAdmin** e importe o arquivo [`database.sql`](database.sql) ou simplesmente acesse:
   `http://localhost/calculadora/backend/setup.php` para o script criar as tabelas e dados automaticamente via PDO!
3. Acesse a aplicação em: `http://localhost/calculadora/index.php`.

---

## 🛡️ Segurança: Uso de PDO contra SQL Injection
Todas as consultas SQL no backend utilizam **PDO com prepared statements** e parâmetros nomeados:

```php
// Exemplo real presente em backend/buscar.php
$stmt = $pdo->prepare("
    SELECT * FROM pokemons 
    WHERE LOWER(nome) = :nome OR id = :id 
    LIMIT 1
");
$stmt->execute([
    ':nome' => strtolower($query),
    ':id'   => is_numeric($query) ? (int)$query : -1
]);
$pokemon = $stmt->fetch();
```

---

## 📱 Responsividade (Mobile-First)
- Utilização do sistema de grid fluido do Bootstrap 5 (`col-12 col-md-6 col-lg-4`).
- Elementos com zonas de toque confortáveis para dispositivos móveis.
- Layout adaptável para telas verticais (smartphones) e horizontais (tablets e desktops).

---

## 📜 Licença
Projeto acadêmico / open source sob licença MIT. Repositório de origem: [Pok-friendos](https://github.com/lucianoms3467-bit/Pok-friendos.git).
