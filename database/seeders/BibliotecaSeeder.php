<?php

namespace Database\Seeders;

use App\Models\Autor;
use App\Models\Editora;
use App\Models\Livro;
use Illuminate\Database\Seeder;

class BibliotecaSeeder extends Seeder
{
    public function run(): void
    {
        $editorasData = [
            ['nome' => "Porto Editora", 'logotipo' => null, 'notas' => "Editora portuguesa focada em literatura, educacao e referencia."],
            ['nome' => "LeYa", 'logotipo' => null, 'notas' => "Grupo editorial com catalogo contemporaneo e classicos."],
            ['nome' => "Relogio D'Agua", 'logotipo' => null, 'notas' => "Especializada em ficcao literaria e ensaio."],
            ['nome' => "Tinta-da-China", 'logotipo' => null, 'notas' => "Nao-ficcao narrativa, historia e cronica."],
            ['nome' => "Alfaguara", 'logotipo' => null, 'notas' => "Selo literario internacional com autores premiados."],
            ['nome' => "Dom Quixote", 'logotipo' => null, 'notas' => "Ficcao literaria portuguesa e estrangeira."],
            ['nome' => "Penguin Classics", 'logotipo' => null, 'notas' => "Classicos universais em edicoes de referencia."],
            ['nome' => "Companhia das Letras", 'logotipo' => null, 'notas' => "Catalogo amplo de ficcao, nao-ficcao e poesia."],
            ['nome' => "Presenca", 'logotipo' => null, 'notas' => "Editora generalista com forte presenca no mercado."],
        ];

        $autoresData = [
            ['nome' => "Jose Saramago", 'foto' => null, 'bibliografia' => "Autor portugues vencedor do Premio Nobel da Literatura em 1998."],
            ['nome' => "Fernando Pessoa", 'foto' => null, 'bibliografia' => "Poeta e ensaista portugues, figura central do modernismo."],
            ['nome' => "Eca de Queiros", 'foto' => null, 'bibliografia' => "Romancista realista portugues, autor de classicos do seculo XIX."],
            ['nome' => "Sophia de Mello Breyner", 'foto' => null, 'bibliografia' => "Poeta portuguesa com obra marcada por mar, etica e liberdade."],
            ['nome' => "Lidia Jorge", 'foto' => null, 'bibliografia' => "Romancista portuguesa contemporanea, Premio FIL 2020."],
            ['nome' => "Valter Hugo Mae", 'foto' => null, 'bibliografia' => "Escritor portugues de prosa poetica e forte experimentacao formal."],
            ['nome' => "Jose Luis Peixoto", 'foto' => null, 'bibliografia' => "Autor portugues de ficcao e poesia, traducao internacional."],
            ['nome' => "Mia Couto", 'foto' => null, 'bibliografia' => "Escritor mocambicano de lingua portuguesa, estilo lirico singular."],
            ['nome' => "Pepetela", 'foto' => null, 'bibliografia' => "Romancista angolano, destaque da literatura africana lusofona."],
            ['nome' => "Machado de Assis", 'foto' => null, 'bibliografia' => "Maior nome do realismo brasileiro, fundador da ABL."],
            ['nome' => "Clarice Lispector", 'foto' => null, 'bibliografia' => "Escritora brasileira de ficcao introspectiva e inovadora."],
            ['nome' => "Jorge Amado", 'foto' => null, 'bibliografia' => "Romancista brasileiro com foco social e cultural da Bahia."],
            ['nome' => "Gabriel Garcia Marquez", 'foto' => null, 'bibliografia' => "Escritor colombiano e Nobel, expoente do realismo magico."],
            ['nome' => "Italo Calvino", 'foto' => null, 'bibliografia' => "Autor italiano de obras alegoricas e metalinguisticas."],
            ['nome' => "George Orwell", 'foto' => null, 'bibliografia' => "Escritor e jornalista ingles, autor de distopias classicas."],
            ['nome' => "Aldous Huxley", 'foto' => null, 'bibliografia' => "Escritor ingles conhecido por ficcao distopica e ensaio."],
            ['nome' => "Margaret Atwood", 'foto' => null, 'bibliografia' => "Escritora canadiana de ficcao especulativa e critica social."],
            ['nome' => "Ursula K. Le Guin", 'foto' => null, 'bibliografia' => "Autora norte-americana de fantasia e ficcao cientifica."],
        ];

        $livrosData = [
            ['isbn' => "9789720046715", 'nome' => "Ensaio Sobre a Cegueira", 'editora' => "Porto Editora", 'sinopse' => "Uma epidemia de cegueira branca expoe a fragilidade da organizacao social.", 'preco' => 18.90, 'autores' => ["Jose Saramago"]],
            ['isbn' => "9789720048726", 'nome' => "Memorial do Convento", 'editora' => "Porto Editora", 'sinopse' => "Ficcao historica que cruza poder, fe e imaginacao no seculo XVIII portugues.", 'preco' => 16.50, 'autores' => ["Jose Saramago"]],
            ['isbn' => "9789723829490", 'nome' => "Mensagem", 'editora' => "Presenca", 'sinopse' => "Livro poetico de Fernando Pessoa sobre mito, destino e identidade portuguesa.", 'preco' => 10.90, 'autores' => ["Fernando Pessoa"]],
            ['isbn' => "9789723821135", 'nome' => "Os Maias", 'editora' => "Presenca", 'sinopse' => "Retrato critico da sociedade lisboeta oitocentista atraves da familia Maia.", 'preco' => 14.20, 'autores' => ["Eca de Queiros"]],
            ['isbn' => "9789897832241", 'nome' => "Livro Sexto", 'editora' => "Relogio D'Agua", 'sinopse' => "Poemas de Sophia onde o mar e a etica surgem como eixos de permanencia.", 'preco' => 11.70, 'autores' => ["Sophia de Mello Breyner"]],
            ['isbn' => "9789896713152", 'nome' => "Misericordia", 'editora' => "Dom Quixote", 'sinopse' => "Narrativa sobre velhice, memoria e dignidade humana.", 'preco' => 17.30, 'autores' => ["Lidia Jorge"]],
            ['isbn' => "9789720049785", 'nome' => "A Maquina de Fazer Espanhois", 'editora' => "LeYa", 'sinopse' => "Um homem idoso revisita o passado e o presente de um pais em transformacao.", 'preco' => 15.80, 'autores' => ["Valter Hugo Mae"]],
            ['isbn' => "9789720043325", 'nome' => "Nenhum Olhar", 'editora' => "LeYa", 'sinopse' => "Romance de linguagem poetica sobre destino e pertenca no Alentejo.", 'preco' => 13.50, 'autores' => ["Jose Luis Peixoto"]],
            ['isbn' => "9789896712438", 'nome' => "Terra Sonambula", 'editora' => "Alfaguara", 'sinopse' => "Viagem por um pais em guerra onde memoria e sonho se confundem.", 'preco' => 14.90, 'autores' => ["Mia Couto"]],
            ['isbn' => "9788535928186", 'nome' => "Memorias Postumas de Bras Cubas", 'editora' => "Companhia das Letras", 'sinopse' => "Narrador defunto questiona moral e elites com ironia radical.", 'preco' => 12.90, 'autores' => ["Machado de Assis"]],
            ['isbn' => "9788535920227", 'nome' => "A Hora da Estrela", 'editora' => "Companhia das Letras", 'sinopse' => "Retrato de Macabea, jovem nordestina invisivel na cidade grande.", 'preco' => 11.90, 'autores' => ["Clarice Lispector"]],
            ['isbn' => "9788535924102", 'nome' => "Capitaes da Areia", 'editora' => "Companhia das Letras", 'sinopse' => "Meninos de rua de Salvador enfrentam pobreza e violencia estrutural.", 'preco' => 13.40, 'autores' => ["Jorge Amado"]],
            ['isbn' => "9788535914844", 'nome' => "Cem Anos de Solidao", 'editora' => "Alfaguara", 'sinopse' => "Saga da familia Buendia em Macondo, marco do realismo magico.", 'preco' => 19.90, 'autores' => ["Gabriel Garcia Marquez"]],
            ['isbn' => "9780141036149", 'nome' => "1984", 'editora' => "Penguin Classics", 'sinopse' => "Distopia sobre vigilancia total, linguagem e manipulacao politica.", 'preco' => 12.30, 'autores' => ["George Orwell"]],
            ['isbn' => "9780060850529", 'nome' => "Brave New World", 'editora' => "Penguin Classics", 'sinopse' => "Sociedade tecnocratica em que estabilidade e consumo suprimem liberdade.", 'preco' => 11.60, 'autores' => ["Aldous Huxley"]],
            ['isbn' => "9780385490818", 'nome' => "The Handmaid's Tale", 'editora' => "Penguin Classics", 'sinopse' => "Distopia teocratica sobre controle do corpo feminino e resistencia.", 'preco' => 13.70, 'autores' => ["Margaret Atwood"]],
            ['isbn' => "9780441478125", 'nome' => "The Left Hand of Darkness", 'editora' => "Penguin Classics", 'sinopse' => "Ficcao cientifica sobre genero, politica e alteridade cultural.", 'preco' => 14.10, 'autores' => ["Ursula K. Le Guin"]],
            ['isbn' => "9789720047026", 'nome' => "Antologia Essencial", 'editora' => "LeYa", 'sinopse' => "Selecao de textos para introducao ao modernismo portugues.", 'preco' => 18.10, 'autores' => ["Fernando Pessoa", "Jose Saramago"]],
            ['isbn' => "9789720047033", 'nome' => "Cronicas do Atlantico", 'editora' => "Tinta-da-China", 'sinopse' => "Coletanea de cronicas sobre paisagem, memoria e mudanca social.", 'preco' => 16.80, 'autores' => ["Lidia Jorge", "Mia Couto"]],
            ['isbn' => "9789720047040", 'nome' => "Fronteiras da Utopia", 'editora' => "Relogio D'Agua", 'sinopse' => "Ensaio literario sobre ficcao politica e futuro tecnologico.", 'preco' => 17.90, 'autores' => ["George Orwell", "Aldous Huxley", "Margaret Atwood"]],
        ];

        $editoras = [];
        foreach ($editorasData as $data) {
            $editora = Editora::create($data);
            $editoras[$editora->nome] = $editora;
        }

        $autores = [];
        foreach ($autoresData as $data) {
            $autor = Autor::create($data);
            $autores[$autor->nome] = $autor;
        }

        $requisicoesPorIsbn = [
            '9789720046715' => 184,
            '9780141036149' => 172,
            '9788535914844' => 151,
            '9789720048726' => 139,
            '9788535924102' => 127,
            '9780060850529' => 121,
            '9789723821135' => 118,
            '9789720049785' => 113,
            '9788535920227' => 107,
            '9789720047033' => 93,
        ];

        foreach ($livrosData as $data) {
            $autoresLivro = $data['autores'];
            $editoraNome = $data['editora'];

            $livro = Livro::create([
                'isbn' => $data['isbn'],
                'nome' => $data['nome'],
                'editora_id' => $editoras[$editoraNome]->id,
                'sinopse' => $data['sinopse'],
                'capa_imagem' => null,
                'preco' => number_format((float) $data['preco'], 2, '.', ''),
                'total_requisicoes' => $requisicoesPorIsbn[$data['isbn']] ?? random_int(24, 96),
            ]);

            $autorIds = collect($autoresLivro)
                ->map(fn (string $nome) => $autores[$nome]->id)
                ->all();

            $livro->autores()->sync($autorIds);
        }
    }
}



