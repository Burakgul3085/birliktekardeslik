import { parseAmount } from './prices';

export function calculateZakat(form, prices, settings) {
    const gold24 = parseAmount(form.gold24) * (prices.gold_24_per_gram || 0);
    const gold22 = parseAmount(form.gold22) * (prices.gold_22_per_gram || 0);
    const gold18 = parseAmount(form.gold18) * (prices.gold_18_per_gram || 0);
    const gold14 = parseAmount(form.gold14) * (prices.gold_14_per_gram || 0);
    const silver = parseAmount(form.silver) * (prices.silver_per_gram || 0);
    const cash = parseAmount(form.cash);
    const bank = parseAmount(form.bank);
    const usd = parseAmount(form.usd) * (prices.usd_try || 0);
    const eur = parseAmount(form.eur) * (prices.eur_try || 0);
    const gbp = parseAmount(form.gbp) * (prices.gbp_try || 0);
    const trade = parseAmount(form.trade);
    const receivables = parseAmount(form.receivables);
    const debts = parseAmount(form.debts);

    const totalAssets = gold24 + gold22 + gold18 + gold14 + silver + cash + bank + usd + eur + gbp + trade + receivables;
    const netWealth = Math.max(0, totalAssets - debts);
    const nisapThreshold = prices.nisap_threshold_try || 0;
    const rate = settings.zakat_rate ?? 0.025;
    const meetsNisap = nisapThreshold > 0 && netWealth >= nisapThreshold;
    const zakatAmount = meetsNisap && form.hawl ? netWealth * rate : 0;

    return {
        breakdown: {
            gold24,
            gold22,
            gold18,
            gold14,
            silver,
            cash,
            bank,
            usd,
            eur,
            gbp,
            trade,
            receivables,
            debts,
        },
        totalAssets,
        netWealth,
        nisapThreshold,
        meetsNisap,
        zakatAmount,
    };
}
