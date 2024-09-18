export const PaymentMethodCard = (
    {
        label,
        icon,
        description,
        onCancel
    }) => {
    return (
        <div className={'wc-ppcp-components-payment-card__container'}>
            <div className={'wc-ppcp-components-payment-card__card'}>
                <img
                    className={'wc-ppcp-components-payment-card__icon'}
                    alt={icon.alt}
                    src={icon.src}/>
                <div className={'wc-ppcp-components-payment-card__description'}>
                    <span>{description}</span>
                </div>
            </div>
            <div
                className={'wc-ppcp-components-payment-card-cancel__container'}>
                <span
                    className={'wc-ppcp-components-payment-card-cancel__label'}
                    onClick={onCancel}
                    onClick={onCancel}>{label}</span>
            </div>
        </div>
    )
}