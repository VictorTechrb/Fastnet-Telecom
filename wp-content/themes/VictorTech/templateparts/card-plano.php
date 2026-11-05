<?php
/**
 * Cartão dinâmico de plano.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$plano_id              = get_the_ID();
$dados                 = fastnet_obter_dados_do_conteudo( $plano_id );
$velocidade            = isset( $dados['velocidade'] ) ? absint( $dados['velocidade'] ) : 0;
$unidade_velocidade    = isset( $dados['unidade_velocidade'] ) && in_array( $dados['unidade_velocidade'], array( 'MEGA', 'GIGA' ), true ) ? $dados['unidade_velocidade'] : 'MEGA';
$preco                 = isset( $dados['preco'] ) ? (float) $dados['preco'] : 0;
$preco_formatado       = number_format_i18n( $preco, 2 );
$beneficios            = isset( $dados['beneficios'] ) ? preg_split( '/\r\n|\r|\n/', $dados['beneficios'] ) : array();
$beneficios            = array_filter( array_map( 'trim', $beneficios ) );
$aplicativos_inclusos  = isset( $args['aplicativos_inclusos'] ) && is_array( $args['aplicativos_inclusos'] ) ? $args['aplicativos_inclusos'] : array();
$aplicativos_adicionais = isset( $args['aplicativos_adicionais'] ) && is_array( $args['aplicativos_adicionais'] ) ? $args['aplicativos_adicionais'] : array();
$configuracoes         = isset( $args['configuracoes'] ) && is_array( $args['configuracoes'] ) ? $args['configuracoes'] : array();
$colunas_apps          = isset( $configuracoes['apps_inclusos_colunas'] ) ? min( 6, max( 1, absint( $configuracoes['apps_inclusos_colunas'] ) ) ) : 4;
$link_contratacao      = ! empty( $dados['link_contratacao'] ) ? $dados['link_contratacao'] : ( isset( $configuracoes['whatsapp_url'] ) ? $configuracoes['whatsapp_url'] : '' );
?>
<article class="cartao-plano-fastnet <?php echo ! empty( $dados['destaque'] ) ? 'cartao-plano-fastnet--destaque' : ''; ?>"
    data-plan-card
    data-plan-price="<?php echo esc_attr( $preco ); ?>"
    data-plan-name="<?php echo esc_attr( get_the_title() ); ?>"
    data-plan-speed="<?php echo esc_attr( $velocidade ); ?>"
    data-plan-speed-unit="<?php echo esc_attr( $unidade_velocidade ); ?>">
    <?php if ( ! empty( $dados['destaque'] ) ) : ?>
        <span class="plano-selo-destaque">Plano em destaque</span>
    <?php endif; ?>

    <?php if ( $velocidade ) : ?>
        <p class="plano-velocidade">
            <strong><?php echo esc_html( number_format_i18n( $velocidade ) ); ?></strong>
            <span><?php echo esc_html( $unidade_velocidade ); ?></span>
        </p>
    <?php endif; ?>

    <p class="plano-preco">
        <span>R$</span>
        <strong><?php echo esc_html( $preco_formatado ); ?></strong>
        <small>/mês</small>
    </p>

    <?php if ( ! empty( $dados['descricao_preco'] ) ) : ?>
        <p class="plano-descricao-preco"><?php echo esc_html( $dados['descricao_preco'] ); ?></p>
    <?php endif; ?>

    <?php if ( $beneficios ) : ?>
        <ul class="plano-beneficios">
            <?php foreach ( $beneficios as $beneficio ) : ?>
                <li><?php echo esc_html( $beneficio ); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ( $aplicativos_inclusos ) : ?>
        <section class="plano-inclusos" aria-label="Aplicativos inclusos neste plano">
            <h4>Incluso no plano:</h4>
            <div class="grade-aplicativos-inclusos" style="<?php echo esc_attr( '--apps-columns:' . $colunas_apps . ';' ); ?>">
                <?php foreach ( $aplicativos_inclusos as $aplicativo_incluso ) : ?>
                    <?php
                    $dados_incluso = fastnet_obter_dados_do_conteudo( $aplicativo_incluso->ID );
                    $logo_id       = isset( $dados_incluso['imagem_id'] ) ? absint( $dados_incluso['imagem_id'] ) : 0;
                    $nome_app      = get_the_title( $aplicativo_incluso->ID );
                    ?>
                    <span class="icone-aplicativo-incluso" title="<?php echo esc_attr( $nome_app ); ?>">
                        <?php if ( $logo_id ) : ?>
                            <?php echo wp_get_attachment_image( $logo_id, 'thumbnail', false, array( 'alt' => $nome_app, 'loading' => 'lazy' ) ); ?>
                        <?php else : ?>
                            <span aria-hidden="true"><?php echo esc_html( function_exists( 'mb_substr' ) ? mb_substr( $nome_app, 0, 1 ) : substr( $nome_app, 0, 1 ) ); ?></span>
                        <?php endif; ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ( $aplicativos_adicionais ) : ?>
        <details class="plano-personalizador">
            <summary>Personalize seu plano</summary>
            <div class="conteudo-personalizador" aria-label="Aplicativos adicionais">
                <p>Selecione apenas o que deseja adicionar:</p>
                <div class="lista-opcoes-aplicativos lista-opcoes-aplicativos--unica">
                    <?php foreach ( $aplicativos_adicionais as $aplicativo ) : ?>
                        <button
                            class="opcao-aplicativo"
                            type="button"
                            data-option-price="<?php echo esc_attr( $aplicativo['preco'] ); ?>"
                            data-option-name="<?php echo esc_attr( $aplicativo['nome'] ); ?>"
                            aria-pressed="false"
                        >
                            <span class="opcao-aplicativo-nome">
                                <?php if ( ! empty( $aplicativo['imagem_id'] ) ) : ?>
                                    <?php echo wp_get_attachment_image( absint( $aplicativo['imagem_id'] ), 'thumbnail', false, array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
                                <?php endif; ?>
                                <span><?php echo esc_html( $aplicativo['nome'] ); ?></span>
                            </span>
                            <span class="opcao-aplicativo-preco">+ R$ <?php echo esc_html( number_format_i18n( $aplicativo['preco'], 2 ) ); ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </details>
    <?php endif; ?>

    <p class="plano-total">
        <span>Total mensal</span>
        <strong>R$ <span data-plan-total><?php echo esc_html( $preco_formatado ); ?></span></strong>
    </p>

    <?php if ( $link_contratacao ) : ?>
        <a
            class="plano-botao-contratar"
            href="<?php echo esc_url( $link_contratacao ); ?>"
            data-plan-contract
            target="_blank"
            rel="noopener noreferrer"
        >Contratar plano</a>
    <?php endif; ?>
</article>
