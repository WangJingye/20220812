module.exports = function(api) {
    api.cache(false);
    const presets = [
        [
            "@babel/preset-env",
            {
                useBuiltIns: "usage",
                targets: {
                    browsers: [
                        "Chrome >= 49",
                        "Firefox >= 45",
                        "Safari >= 10",
                        "Edge >= 13",
                        "iOS >= 10",
                        "Electron >= 0.36"
                    ]
                }
            }
        ]
    ];

    const  plugins=[
        ["@babel/plugin-transform-runtime",{"corejs": 2}],
        "@babel/plugin-transform-object-assign",
        ["@babel/plugin-proposal-decorators", { "legacy": true }],
        "@babel/plugin-proposal-class-properties"
    ]

    return { presets,plugins };
};