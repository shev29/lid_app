<head>
    <script src="https://unpkg.com/@univerjs/umd/lib/univer.full.umd.js"></script>
    <script src="https://unpkg.com/@univerjs/umd/lib/locale/en-US.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/@univerjs/umd/lib/univer.css">
  </head>
  <body>
    <div id="app"></div>

    <script>
      var {
        UniverCore,
        UniverDesign,
        UniverEngineRender,
        UniverEngineFormula,
        UniverDocs,
        UniverDocsUi,
        UniverUi,
        UniverSheets,
        UniverSheetsUi,
        UniverSheetsNumfmt,
        UniverSheetsFormula,
        UniverFacade,
      } = window

      var univer = new UniverCore.Univer({
        theme: UniverDesign.defaultTheme,
        locale: UniverCore.LocaleType.EN_US,
        locales: {
          [UniverCore.LocaleType.EN_US]: UniverUMD['en-US'],
        },
      });

      univer.registerPlugin(UniverEngineRender.UniverRenderEnginePlugin);
      univer.registerPlugin(UniverEngineFormula.UniverFormulaEnginePlugin);

      univer.registerPlugin(UniverUi.UniverUIPlugin, {
        container: "app",
      });

      univer.registerPlugin(UniverDocs.UniverDocsPlugin);
      univer.registerPlugin(UniverDocsUi.UniverDocsUIPlugin);

      univer.registerPlugin(UniverSheets.UniverSheetsPlugin);
      univer.registerPlugin(UniverSheetsUi.UniverSheetsUIPlugin);
      univer.registerPlugin(UniverSheetsNumfmt.UniverSheetsNumfmtPlugin);
      univer.registerPlugin(UniverSheetsFormula.UniverSheetsFormulaPlugin);

      univer.createUnit(UniverCore.UniverInstanceType.UNIVER_SHEET, {})

      const univerAPI = UniverFacade.FUniver.newAPI(univer)
    </script>
  </body>