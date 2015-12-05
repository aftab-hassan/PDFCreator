###########################################################################
#Purpose : Integrated script which will do the following
#          1.Data validation
#	   2.Train Bayesian Network Model
#	   3.Do Prediction
#	   4.Generates rules in rules.csv
#	   5.Compute metrics for test data
#	   6.Write results to results.csv

#To run  : Rscript bn.R <inputfile>
###########################################################################

#turn off warnings
options(warn=-1)

#user-defined functions
lastn <- function(x, n)
{
  substr(x, nchar(x)-n+1, nchar(x))
}

buildModel <- function()
{
hcbn = hc(bndf, score='bic',restart = 0)
hcbn.fitted = bn.fit(hcbn, bndf, method='bayes')
hcbn.grain <<- as.grain(hcbn.fitted)
cat(paste("Step 2 of 5 completed : Building models done\n\n"));
save.image(file = './steps/step2.RData')
}

doRecommendation <- function()
{
    #for now
    predictdf = predictdf[which(predictdf$thirtyday==0),]
    predictdf = predictdf[1:1000,]
predictor1 = names(df)[-c(grep("MED",names(df)),grep("thirtyday",names(df)))]
predictor2 = names(df)[-grep("thirtyday",names(df))]

registerDoMC(8)
hcpred1 <- list()
hcpred2 <- list()

for (i in 1:1) 
{
    start <- 1
    end <- nrow(predictdf)
    
    hcpred1[[i]] <- foreach (j = start:min(end, nrow(predictdf)), .combine=rbind) %dopar% 
    {
		cat(paste("only non-medications predictors","j==",j,"\n"));
		predict(hcbn.grain, response = c("thirtyday"), newdata = predictdf[j, ], predictors = predictor1, type = "distribution")$pred$thirtyday;
    }

    hcpred2[[i]] <- foreach (j = start:min(end, nrow(predictdf)), .combine=rbind) %dopar% 
    {
		cat(paste("all predictors","j==",j,"\n"));
	    predict(hcbn.grain, response = c("thirtyday"), newdata = predictdf[j, ], predictors = predictor2, type = "distribution")$pred$thirtyday;
    }
}

pWithout <<- c()
pWith <<- c()
for (i in 1:1) 
{
	pWithout <<- rbind(pWithout, hcpred1[[i]])
	pWith <<- rbind(pWith, hcpred2[[i]])
}

save.image(file = './steps/step3.RData');
cat(paste("Step 3 of 5 completed : Building models done\n\n"));
}

generateRules <- function()
{
#applying the condition and forming 'rules'
cat(paste("Step 4 of 5 initiated : Generating rules...\n"));

predictdf$RiskScoreWithoutMedications = round(pWithout[, 2]*100)
predictdf$RiskScoreWithMedications = round(pWith[, 2]*100)
ruleIndex = which(predictdf$RiskScoreWithMedications < predictdf$RiskScoreWithoutMedications)#probability of not getting readmitted(0, here 1) should be higher with medications
rules = predictdf[ruleIndex,]
rules = rules[which(rules$thirtyday == 0),]
#for(i in 52:101){rules[,i] = as.integer(as.character(rules[,i]))}
#rules = rules[which(rowSums(rules[,52:101]) > 0),]

message(nrow(rules), ' rules found')
write.csv(rules, file = './rules.csv',row.names=FALSE)
save.image(file = './steps/step4.RData')
cat(paste("Step 4 of 5 completed : Rules generated\n\n"));
}

#read command line arguments
inputfile = "MHS_CHF_Medications_NoNA.RDS"
df = readRDS(inputfile)

#three types of columns
response = c('thirtyday','sixtyday','ninetyday','nextLOS','two_LOS','four_LOS','six_LOS','seven_LOS','nextcost','three_mortality','six_mortality','nine_mortality','twelve_mortality')
ignore = c('HID','PID','EID','admitDT','dischargeDT','nextadmitDT','daystonext')
responsehere = 'thirtyday'
df$sixtyday = NULL
df$ninetyday = NULL
df$nextLOS = NULL
df$two_LOS = NULL
df$four_LOS = NULL
df$six_LOS = NULL
df$seven_LOS = NULL
df$nextcost = NULL
df$three_mortality = NULL
df$six_mortality = NULL
df$nine_mortality = NULL
df$twelve_mortality = NULL
df$HID = NULL
df$PID = NULL
df$EID = NULL
df$admitDT = NULL
df$dischargeDT = NULL
df$nextadmitDT = NULL
df$daystonext = NULL
df$hf_count = NULL
df$MED.28689 = NULL
for(i in 1:ncol(df))
{
    df[,i] = as.factor(df[,i])
}

#removing rows which don't have a single medication
dfmed = df[,grep("MED.",names(df))]
for(i in 1:ncol(dfmed))
{
    dfmed[,i] = as.character(dfmed[,i])
    dfmed[,i] = as.numeric(dfmed[,i])
}
AtleastOneMedPatients = which(rowSums(dfmed) > 0)
AtleastOnceUsedMedications = which(colSums(dfmed) > 0)
NonMedicationsColumns = setdiff(names(df),names(df)[grep("MED.",names(df))])
df = df[AtleastOneMedPatients,c(NonMedicationsColumns,names(AtleastOnceUsedMedications))]

#split data into two types of train data, one for building the model called(bndf) and one for prediction called (predictdf)
#use 80% of the data for training., 20% for testing
traindf = df[1:(0.8*nrow(df)),]
testdf = df[((0.8*nrow(df))+1):nrow(df),]

#from the training data, use 50% for building the network(learning conditional probabilities)
bndf = traindf[1:(0.5*nrow(traindf)),]
predictdf = traindf

#load necessary packages
suppressMessages(library(bnlearn))
suppressMessages(library(gRain))
suppressMessages(library(foreach))
suppressMessages(library(doMC))

buildModel();
doRecommendation();
generateRules();