export const registerSettings = (id, clazz) => {
    new clazz({
        id,
        prefix: `woocommerce_${id}_`,
        data: wcPPCPSettings?.settings?.[id] || {}
    })
}
