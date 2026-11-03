<?php
$velocidade = get_field( 'plano_velocidade' );
$unidade    = get_field( 'plano_unidade' );
$preco      = get_field( 'plano_preco' );
$obs_preco  = get_field( 'plano_obs_preco' );
$destaque   = get_field( 'plano_destaque' );
$aplicativos = get_field( 'plano_aplicativos' );

$apps_array = $aplicativos ? preg_split( '/\r\n|\r|\n/', $aplicativos ) : array();
?>

<article class="card-plano-telecom<?php echo $destaque ? ' card-plano-destaque' : ''; ?>">
    <?php if ( $destaque ) : ?>
        <span class="selo-plano-destaque">MAIS ESCOLHIDO</span>
    <?php endif; ?>

    <div class="cabecalho-card-plano" style="text-align: center; border-bottom: 1px solid var(--cinza-linha); padding-bottom: 20px;">
        <h3 class="nome-plano" style="font-size: 1.2rem; color: var(--texto-corpo); margin-bottom: 5px;">Plano <?php echo esc_html($velocidade); ?></h3>
        <div class="valor-mega" style="display: flex; align-items: baseline; justify-content: center; gap: 5px;">
            <strong class="velocidade-plano" style="font-size: 3.5rem; color: var(--vermelho-fastnet); line-height: 1;"><?php echo esc_html( $velocidade ); ?></strong>
            <span style="font-size: 1.2rem; font-weight: 700; color: var(--vermelho-fastnet);"><?php echo esc_html($unidade); ?></span>
        </div>
        
        <div class="preco-container" style="margin-top: 15px;">
            <div style="display: flex; align-items: baseline; justify-content: center; gap: 4px;">
                <span style="font-size: 1.1rem; font-weight: 700;">R$</span>
                <strong style="font-size: 2.8rem; line-height: 1;"><?php echo esc_html( $preco ); ?></strong>
                <span style="font-size: 0.9rem; color: var(--texto-corpo);">/mês</span>
            </div>
            <?php if ($obs_preco): ?>
                <p style="font-size: 0.75rem; color: var(--texto-apoio); margin-top: 5px;"><?php echo esc_html($obs_preco); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="corpo-card-plano" style="padding: 20px 0; flex: 1;">
        <p style="font-weight: 700; font-size: 0.85rem; margin-bottom: 15px; color: var(--preto-tech);">Aplicativos inclusos:</p>
        <div class="lista-apps-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
            <?php foreach ( array_slice($apps_array, 0, 6) as $app ) : ?>
                <div class="app-item-icon" style="background: var(--bg-suave); border-radius: 8px; padding: 8px; text-align: center; border: 1px solid var(--cinza-linha);">
                    <div style="font-size: 1.2rem; margin-bottom: 4px;">📱</div>
                    <span style="font-size: 0.6rem; font-weight: 700; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo esc_html( trim( $app ) ); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="rodape-card-plano" style="margin-top: auto;">
        <a class="botao-plano" href="https://wa.me/557930454880" style="background: var(--vermelho-fastnet); color: white; display: block; text-align: center; padding: 15px; border-radius: 50px; text-decoration: none; font-weight: 800; transition: var(--transicao);">
            ASSINAR AGORA
        </a>
    </div>
</article>































































