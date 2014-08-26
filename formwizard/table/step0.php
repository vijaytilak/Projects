
    <table width="100%" id="maincontainer" class="pure-table" border="0" cellspacing="0" cellpadding="3">
      <caption>
        Canopy Main Configuration
      </caption>
      <tr><td rowspan="10"><div id="StepTableContainer" style="width: 500px;"></div></td></tr>
      <tr>
      <td style="background:#E2E0FF"><label for="freestanding">Free Standing:</label></td>
      <td colspan="3" style="text-align:left">
        <label style="margin-right:73px"><input type="radio" name="freestanding" value="0" id="freestanding_0" checked/>No</label> 
        <label><input type="radio" name="freestanding" value="1" id="freestanding_1" />Yes</label>
      </td>
      </tr>
      <tr>
      <td style="background:#E2E1FF"><label for="canopytype">Canopy Type:</label></td>
      <td colspan="3" style="text-align:left">
        <label style="margin-right:10px"><input type="radio" name="canopytype" value="0" id="canopytype_0" checked/>Back Stepped</label>
        <label style="margin-right:10px"><input type="radio" name="canopytype" value="1" id="canopytype_1" />Front Stepped</label>
        <label><input type="radio" name="canopytype" value="2" id="canopytype_2" />Front & Back Stepped</label>
      </td>
      </tr>      
      <tr>
      <td style="background:#E2E1FF"><label for="openingtype">Opening Type:</label></td>
      <td colspan="2" style="text-align:left">
        <label style="margin-right:58px"><input type="radio" name="openingtype" value="0" id="openingtype_0" checked/>Same</label> 
        <label><input type="radio" name="openingtype" value="1" id="openingtype_1" />Mixed</label>
      </td>
      </tr> 
      <tr id="rowopeningtypeleft" style="display:none">
          <td style="background:#E9EEFF">Left</td>
          <td style="text-align:left"><input type="hidden" id="openingtypeleft" name="openingtypeleft" /></td>
          <td style=""><input type="hidden" id="openingtypeleftfabric" name="openingtypeleftfabric" /></td>
      </tr>
      <tr id="rowopeningtypefront" style="display:none">
          <td style="background:#E9EEFF">Front</td>
          <td style="text-align:left"><input type="hidden" id="openingtypefront" name="openingtypefront" /></td>
          <td style=""><input type="hidden" id="openingtypefrontfabric" name="openingtypefrontfabric" /></td>
      </tr>
      <tr id="rowopeningtyperight" style="display:none">
          <td style="background:#E9EEFF">Right</td>
          <td style="text-align:left"><input type="hidden" id="openingtyperight" name="openingtyperight" /></td>
          <td style=""><input type="hidden" id="openingtyperightfabric" name="openingtyperightfabric" /></td>
      </tr>
      <tr id="rowopeningtyperear" style="display:none">
          <td style="background:#E9EEFF">Rear</td>
          <td style="text-align:left"><input type="hidden" id="openingtyperear" name="openingtyperear" /></td>
          <td style=""><input type="hidden" id="openingtyperearfabric" name="openingtyperearfabric" /></td>
      </tr>
      <tr id="rowopeningtypeselect">
          <td style="background:#E9EEFF">Select</td>
          <td style="text-align:left"><input type="hidden" id="openingtypeselect" name="openingtypeselect" /></td>
          <td style=""><input type="hidden" id="openingtypeselectfabric" name="openingtypeselectfabric" /></td>
      </tr>
    </table>
    
    
    <div id="MessageContainer0" style="width: 1000px; background:#8ACBC8; color:#333"></div>
    <input type="hidden" name="quoteaction" value="servervalidatestep0" id="quoteaction" />
    <input type="hidden" name="quoteid" value="<?php echo $quoteid; ?>" id="quoteid" />
